"""Layout regressions. Uses the PHP CLI; no database or Selenium required."""
from html.parser import HTMLParser
from pathlib import Path
import json
import re
import shutil
import subprocess
import unittest
from urllib.parse import urlsplit

ROOT = Path(__file__).resolve().parents[1]
PHP = shutil.which('php')
INCLUDES = re.compile(
    r'''\b(?:include|include_once|require|require_once)\s+__DIR__\s*\.\s*['"]([^'"]+)['"]'''
)


class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.urls = []

    def handle_starttag(self, tag, attrs):
        self.urls.extend(value for key, value in attrs if key in {'src', 'href', 'action'} and value)


class StructureTests(unittest.TestCase):
    def test_source_text_is_utf8_without_broken_vietnamese(self):
        broken_text = re.compile(
            r'[\u0080-\u009f\ufffd]|[\u00e1][\u00ba\u00bb]|'
            r'[\u00c2\u00c3\u00c4\u00c5\u00c6\u0102][\u0080-\u00bf\u0100-\u017f\u2000-\u20ff]'
        )
        for source in ROOT.rglob('*'):
            if not source.is_file() or source.suffix not in {'.php', '.css', '.js', '.py', '.md'} or '.git' in source.parts:
                continue
            with self.subTest(source=str(source.relative_to(ROOT))):
                text = source.read_bytes().decode('utf-8')
                self.assertNotRegex(text, broken_text)

    def test_root_layout(self):
        self.assertEqual(
            {p.name for p in ROOT.iterdir() if not p.name.startswith('.')},
            {'admin', 'assets', 'auth', 'cart', 'checkout', 'config', 'customer',
             'icons', 'images', 'includes', 'pages', 'products', 'tests', 'index.php', 'README.md'},
        )
        self.assertFalse((ROOT / 'assets/images').exists())
        self.assertFalse((ROOT / 'admin/icons').exists())

    def test_all_includes_exist(self):
        for source in ROOT.rglob('*.php'):
            for target in INCLUDES.findall(source.read_text(encoding='utf-8-sig')):
                with self.subTest(source=str(source.relative_to(ROOT)), target=target):
                    self.assertTrue((source.parent / target.lstrip('/')).is_file())

    def test_no_old_api_routes(self):
        for source in ROOT.rglob('*.php'):
            with self.subTest(source=str(source.relative_to(ROOT))):
                self.assertNotRegex(source.read_text(encoding='utf-8-sig'), r'server/api/|pulsetech_admin/')

    def test_admin_menu_destinations_exist(self):
        for name in ('sidebar.php', 'admin.php'):
            source = ROOT / 'admin' / name
            for target in re.findall(r"'href'\s*=>\s*'([^']+\.php)'", source.read_text(encoding='utf-8')):
                with self.subTest(source=name, target=target):
                    self.assertTrue((source.parent / target).is_file())

    @unittest.skipUnless(PHP, 'PHP CLI is not installed')
    def test_php_syntax(self):
        for source in ROOT.rglob('*.php'):
            with self.subTest(source=str(source.relative_to(ROOT))):
                result = subprocess.run([PHP, '-l', str(source)], capture_output=True)
                self.assertEqual(result.returncode, 0, result.stdout + result.stderr)

    def run_php(self, code):
        result = subprocess.run(
            [PHP], input='<?php\n' + code, cwd=ROOT / 'tests',
            text=True, encoding='utf-8', capture_output=True,
        )
        self.assertEqual(result.returncode, 0, result.stderr)
        self.assertEqual(result.stderr, '')
        return result.stdout

    def request_context(self, prefix, script):
        return (
            '$_SERVER["SCRIPT_FILENAME"] = ' + json.dumps((ROOT / script).as_posix()) + ';\n'
            '$_SERVER["SCRIPT_NAME"] = ' + json.dumps(prefix + '/' + script) + ';\n'
        )

    @unittest.skipUnless(PHP, 'PHP CLI is not installed')
    def test_shared_navigation_at_root_and_subdirectory(self):
        for prefix in ('', '/Web', '/shop/Web'):
            for script in ('index.php', 'cart/cart.php', 'products/categories/Alliphone.php'):
                with self.subTest(prefix=prefix, script=script):
                    output = self.run_php(
                        self.request_context(prefix, script)
                        + 'include ' + json.dumps((ROOT / 'includes/header.php').as_posix()) + ';\n'
                        + 'include ' + json.dumps((ROOT / 'includes/footer.php').as_posix()) + ';\n'
                    )
                    links = Links()
                    links.feed(output)
                    self.assertGreater(len(links.urls), 15)
                    for url in links.urls:
                        if urlsplit(url).scheme or url.startswith(('#', '//')):
                            continue
                        path = urlsplit(url).path
                        self.assertTrue(path.startswith(prefix + '/'), url)
                        self.assertTrue((ROOT / path[len(prefix) + 1:]).is_file(), url)
                    self.assertIn(prefix + '/auth/login.php', output)
                    self.assertIn('/auth/api/me.php', output)
                    self.assertIn('Sửa chữa', output)
                    self.assertIn('Đăng nhập', output)
                    self.assertIn('Đăng xuất', output)
                    self.assertIn('Tìm kiếm sản phẩm', output)

    @unittest.skipUnless(PHP, 'PHP CLI is not installed')
    def test_server_paths_with_repeated_separators(self):
        script_file = (ROOT / 'products/categories/Alliphone.php').as_posix().replace('/', '//')
        output = self.run_php(
            '$_SERVER["SCRIPT_FILENAME"] = ' + json.dumps(script_file) + ';\n'
            '$_SERVER["SCRIPT_NAME"] = "/Web/products/categories/Alliphone.php";\n'
            'require ' + json.dumps((ROOT / 'config/paths.php').as_posix()) + ';\n'
            'echo app_url("icons/search.svg");'
        )
        self.assertEqual(output, '/Web/icons/search.svg')

    @unittest.skipUnless(PHP, 'PHP CLI is not installed')
    def test_uploaded_images_use_project_directory(self):
        for prefix in ('', '/Web'):
            with self.subTest(prefix=prefix):
                output = self.run_php(
                    self.request_context(prefix, 'products/detail/product-detail-iphone.php')
                    + 'require ' + json.dumps((ROOT / 'products/product_images_helper.php').as_posix()) + ';\n'
                    + '''echo json_encode([
                        product_gallery_directory(),
                        product_image_file('assets/images/product_gallery/example.png'),
                        product_image_url('assets/images/product_gallery/example.png'),
                        product_image_url('images/product_gallery/example.png'),
                        product_image_url('')
                    ]);'''
                )
                directory, filename, legacy_url, current_url, empty_url = json.loads(output)
                self.assertEqual(Path(directory), ROOT / 'images/product_gallery')
                self.assertEqual(Path(filename), ROOT / 'images/product_gallery/example.png')
                self.assertEqual(legacy_url, prefix + '/images/product_gallery/example.png')
                self.assertEqual(current_url, legacy_url)
                self.assertEqual(empty_url, '')


if __name__ == '__main__':
    unittest.main()

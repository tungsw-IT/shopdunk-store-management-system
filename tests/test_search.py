import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

SEARCH_URL = "http://localhost/Web/pages/search.php"
SEARCH_INPUT = "query"
DELAY = 1

chrome_options = Options()
chrome_options.add_argument('--disable-logging')
chrome_options.add_argument('--log-level=3')
chrome_options.add_experimental_option('excludeSwitches', ['enable-logging'])

TEST_CASES = [
    ("iPhone", "success"),

]


def open_search_overlay(driver):
    search_btn = WebDriverWait(driver, 10).until(
        EC.element_to_be_clickable(
            (By.CSS_SELECTOR, ".icon-button.white img[alt='Search']")
        )
    )
    driver.execute_script("arguments[0].click();", search_btn)
    time.sleep(0.5)


def run_test_case(driver, query, expected, idx):
    driver.get(SEARCH_URL)

    try:
        open_search_overlay(driver)
        WebDriverWait(driver, 10).until(
            EC.visibility_of_element_located((By.ID, "searchOverlay"))
        )
        search_box = WebDriverWait(driver, 10).until(
            EC.element_to_be_clickable((By.NAME, SEARCH_INPUT))
        )
        search_box.click()
        search_box.clear()
        search_box.send_keys(query)
        search_box.submit()

        time.sleep(DELAY)
        page_source = driver.page_source.lower()

        if expected == "success":
            items = driver.find_elements(By.CLASS_NAME, "search-item")

            if len(items) > 0:
                print(f"[PASS] TC{idx}: Có {len(items)} kết quả")
                return True
            else:
                print(f"[FAIL] TC{idx}: Không có kết quả")
                return False

        elif expected == "not_found":
            if "không tìm thấy sản phẩm nào phù hợp" in page_source:
                print(f"[PASS] TC{idx}: Không tìm thấy sản phẩm")
                return True
            else:
                print(f"[FAIL] TC{idx}: Sai thông báo không tìm thấy")
                return False

        elif expected == "empty":
            if "vui lòng nhập từ khóa để tìm kiếm" in page_source:
                print(f"[PASS] TC{idx}: Vui lòng nhập dữ liệu tìm kiếm")
                return True
            else:
                print(f"[FAIL] TC{idx}: Không hiển thị cảnh báo empty")
                return False

    except Exception as e:
        print(f"[ERROR] TC{idx}: {e}")
        return False


if __name__ == "__main__":
    driver = webdriver.Chrome(options=chrome_options)

    passed = 0
    failed = 0

    try:
        for idx, (query, expected) in enumerate(TEST_CASES, start=1):
            result = run_test_case(driver, query, expected, idx)

            if result:
                passed += 1
            else:
                failed += 1

            time.sleep(DELAY)

        print("\n" + "=" * 50)
        print(f"TỔNG KẾT: {len(TEST_CASES)} test case")
        print(f"PASS: {passed}")
        print(f"FAIL: {failed}")
        print("=" * 50)

    finally:
        driver.quit()
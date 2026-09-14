import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

# Tắt log thừa
os.environ['WDM_LOG_LEVEL'] = '0'

# Cấu hình Chrome options để bỏ log thừa
chrome_options = Options()
chrome_options.add_argument('--disable-logging')
chrome_options.add_argument('--log-level=3')
chrome_options.add_experimental_option('excludeSwitches', ['enable-logging'])

# Cấu hình
LOGIN_URL = "http://localhost/Web/auth/login.php"
EMAIL_INPUT = "email"
PASSWORD_INPUT = "password" 

TEST_CASES = [
    ("xuan2108@gmail.com", "Xuan12345", "success"),  # TC1
    ("xuan2108@gmail.com", "", "missing_info"),           # TC2
    ("xuan2108@gmail.com", "abc", "invalid"),      # TC3
    ("", "Xuan12345", "missing_info"),            # TC4
    ("", "", "missing_info"),                         # TC5
    ("", "wrongpass", "missing_info"),                # TC6
    ("sai@gmail.com", "validpassword", "invalid"), # TC7
    ("sai@gmail.com", "", "missing_info"),         # TC8
    ("sai@gmail.com", "wrongpass", "invalid"),     # TC9
]

def run_test_case(driver, email, password, expected, idx):
    driver.get(LOGIN_URL)
    time.sleep(1)
    driver.find_element(By.NAME, EMAIL_INPUT).clear()
    driver.find_element(By.NAME, EMAIL_INPUT).send_keys(email)
    driver.find_element(By.NAME, PASSWORD_INPUT).clear()
    driver.find_element(By.NAME, PASSWORD_INPUT).send_keys(password)
    driver.find_element(By.XPATH, "//button[text()='Đăng nhập']").click()
    
    time.sleep(1)
    
    current_url = driver.current_url
    page_source = driver.page_source
    
    try:
        if expected == "success":
            assert "index.php" in current_url
            print(f"[PASS] TC{idx}: Email hợp lệ + Mật khẩu hợp lệ => Đăng nhập thành công")
            return True
        elif expected == "invalid":
            assert "auth_login.php" in current_url
            error_keywords = ["sai", "tồn tại", "không hợp lệ", "không đúng"]
            assert any(keyword in page_source.lower() for keyword in error_keywords)
            print(f"[PASS] TC{idx}: Email/Mật khẩu không hợp lệ => Hiển thị lỗi đúng")
            return True
        elif expected == "missing_info":
            assert ("Vui lòng nhập" in page_source or "không được để trống" in page_source)
            print(f"[PASS] TC{idx}: Thiếu thông tin => Hiển thị cảnh báo đúng")
            return True
    except AssertionError:
        print(f"[FAIL] TC{idx}: Email='{email}', Mật khẩu='{password}'")
        return False

if __name__ == "__main__":
    driver = webdriver.Chrome(options=chrome_options)
    passed = 0
    failed = 0
    idx = 0
    try:
        for email, password, expected in TEST_CASES:
            idx += 1
            result = run_test_case(driver, email, password, expected, idx)
            if result:
                passed += 1
            else:
                failed += 1
        print("\n" + "="*50)
        print(f"TỔNG KẾT: {len(TEST_CASES)} test case")
        print(f" PASS: {passed}")
        print(f" FAIL: {failed}")
        print("="*50)
    finally:
        driver.quit()


import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

SIGNUP_URL = "http://localhost/Web/auth/signup.php"
DELAY = 1

os.environ['WDM_LOG_LEVEL'] = '0'

chrome_options = Options()
chrome_options.add_argument('--disable-logging')
chrome_options.add_argument('--log-level=3')
chrome_options.add_experimental_option('excludeSwitches', ['enable-logging'])

TEST_CASES = [

    ("DEMO26@gmail.com", "0923456619", "Aa12345", "Aa12345", "success"),   # TC1
    ("test@gmail.com", "", "", "", "missing"),                           # TC2
    ("test@gmail.com", "0123456789", "", "", "missing"),                 # TC3
    ("abc@gmail", "0923456619", "Aa12345", "Aa12345", "invalid"),        # TC4
    ("test@gmail.com", "123", "Aa12345", "Aa12345", "invalid"),          # TC5
    ("test@gmail.com", "0123456789", "abc", "abc", "invalid"),           # TC6
    ("test@gmail.com", "0123456789", "Aa12345", "Sai123", "invalid"),    # TC7
    ("abc", "0123456789", "Aa12345", "Aa12345", "invalid"),              # TC8
    ("", "0123456789", "Aa12345", "Aa12345", "missing"),                 # TC9
]

def run_test_case(driver, email, phone, password, confirm, expected, idx):

    driver.get(SIGNUP_URL)
    time.sleep(DELAY)

    try:
        driver.find_element(By.NAME, "email").clear()
        driver.find_element(By.NAME, "email").send_keys(email)

        driver.find_element(By.NAME, "phone").clear()
        driver.find_element(By.NAME, "phone").send_keys(phone)

        driver.find_element(By.NAME, "password").clear()
        driver.find_element(By.NAME, "password").send_keys(password)

        driver.find_element(By.NAME, "confirm_password").clear()
        driver.find_element(By.NAME, "confirm_password").send_keys(confirm)

        driver.find_element(By.XPATH, "//button[text()='Đăng ký']").click()
        time.sleep(DELAY)

        current_url = driver.current_url.lower()
        page = driver.page_source.lower()

        if expected == "success":
            if "login" in current_url:
                print(f"[PASS] TC{idx}: Đăng ký thành công")
                return True
            else:
                print(f"[FAIL] TC{idx}: Đăng ký KHÔNG thành công")
                return False
        elif expected == "missing":
            if "vui lòng nhập đầy đủ thông tin" in page:
                print(f"[PASS] TC{idx}: Báo thiếu thông tin đúng")
                return True
            else:
                print(f"[FAIL] TC{idx}: Không báo thiếu thông tin")
                return False

        elif expected == "invalid":
            invalid_keywords = [
                "không hợp lệ",
                "10 chữ số",
                "mật khẩu phải",
                "không khớp"
            ]

            if any(k in page for k in invalid_keywords):
                print(f"[PASS] TC{idx}: Báo sai định dạng đúng")
                return True
            else:
                print(f"[FAIL] TC{idx}: Không báo lỗi định dạng")
                return False

    except Exception:
        print(f"[FAIL] TC{idx}: Lỗi hệ thống")
        return False

if __name__ == "__main__":
    driver = webdriver.Chrome(options=chrome_options)

    passed = 0
    failed = 0

    try:
        for idx, tc in enumerate(TEST_CASES, start=1):
            

            result = run_test_case(driver, *tc, idx)

            if result:
                passed += 1
            else:
                failed += 1

            time.sleep(DELAY)

        print("\n" + "="*50)
        print(f"TỔNG KẾT: {len(TEST_CASES)} test case")
        print(f"PASS: {passed}")
        print(f"FAIL: {failed}")
        print("="*50)

    finally:
        driver.quit()




import time
import os
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

BASE_URL = "http://localhost/Web/admin/"
LOGIN_URL = BASE_URL + "login.php"
ADD_URL = BASE_URL + "addcustomer.php"

EMAIL = "admin@gmail.com"
PASSWORD = "Aa12345"

DELAY = 1

os.environ['WDM_LOG_LEVEL'] = '0'

chrome_options = Options()
chrome_options.add_argument('--disable-logging')
chrome_options.add_argument('--log-level=3')
chrome_options.add_experimental_option('excludeSwitches', ['enable-logging'])

TEST_CASES = [
    ("Nguyen Anh Tu 05", "Nam", "tu05@gmail.com", "0352879857", "Hanoi", "success"),
    ("", "Nam", "tc02@gmail.com", "0123456789", "Hanoi", "missing"),
    ("Nguyen Van Anh", "", "tc03@gmail.com", "0123456789", "Hanoi", "missing"),
    ("Nguyen Van Anh", "Nam", "", "0123456789", "Hanoi", "missing"),
    ("Nguyen Van Anh", "Nam", "tc05@gmail.com", "", "Hanoi", "missing"),
    ("Nguyen Van Anh", "Nam", "tc06@gmail.com", "0123456789", "", "missing"),
    ("Nguyen Van Anh", "Nam", "abc@gmail", "0123456789", "Hanoi", "invalid"),
    ("Nguyen Van Anh", "Nam", "tc08@gmail.com", "123", "Hanoi", "invalid"),
    ("Nguyen Van Anh", "Nam", "tc01@gmail.com", "0999999999", "Hanoi", "duplicate_email"),
    ("Nguyen Van Anh", "Nam", "tc10@gmail.com", "0123456789", "Hanoi", "duplicate_phone"),
]

def login(driver, wait):
    print("Đang login...")

    driver.get(LOGIN_URL)
    time.sleep(DELAY)

    print("URL:", driver.current_url)

    email_input = wait.until(EC.presence_of_element_located(
        (By.XPATH, "//input[@type='email' or @type='text']")
    ))

    password_input = driver.find_element(By.XPATH, "//input[@type='password']")

    email_input.clear()
    email_input.send_keys(EMAIL)

    password_input.clear()
    password_input.send_keys(PASSWORD)

    driver.find_element(By.XPATH, "//button").click()

    time.sleep(DELAY)
    print("Login xong")

def run_test_case(driver, wait, name, gender, email, phone, address, expected, idx):
    driver.get(ADD_URL)

    try:
        name_input = wait.until(EC.presence_of_element_located((By.NAME, "name")))

        driver.find_element(By.NAME, "name").clear()
        driver.find_element(By.NAME, "name").send_keys(name)

        driver.find_element(By.NAME, "gender").send_keys(gender)

        driver.find_element(By.NAME, "email").clear()
        driver.find_element(By.NAME, "email").send_keys(email)

        driver.find_element(By.NAME, "phone").clear()
        driver.find_element(By.NAME, "phone").send_keys(phone)

        driver.find_element(By.NAME, "address").clear()
        driver.find_element(By.NAME, "address").send_keys(address)

        driver.find_element(By.XPATH, "//button[text()='Thêm']").click()
        time.sleep(DELAY)

        current_url = driver.current_url

        if expected == "success":
            try:
                time.sleep(1)

                if not "customer.php" in driver.current_url:
                    print(f"[FAIL] TC{idx}: Không chuyển sang customer.php")
                    return False
                toast = driver.find_elements(By.ID, "toast-success")
                if not toast or not toast[0].is_displayed():
                    print(f"[FAIL] TC{idx}: Không thêm khách hàng thành công")
                    return False

                print(f"[PASS] TC{idx}: Thêm khách hàng thành công")
                return True

            except Exception as e:
                print(f"[FAIL] TC{idx}: Lỗi khi kiểm tra success -> {e}")
                return False
        elif expected in ["missing", "invalid"]:
            error_box = driver.find_element(By.ID, "form-error-summary")
            assert error_box.is_displayed()
            print(f"[PASS] TC{idx}: Hiển thị lỗi đúng")
            return True

        elif expected == "duplicate_email":
            error = driver.find_element(By.ID, "server-error")
            msg = error.get_attribute("data-message").lower()
            assert "email đã tồn tại" in msg
            print(f"[PASS] TC{idx}: Trùng email")
            return True

        elif expected == "duplicate_phone":
            error = driver.find_element(By.ID, "server-error")
            msg = error.get_attribute("data-message").lower()
            assert "số điện thoại đã tồn tại" in msg
            print(f"[PASS] TC{idx}: Trùng SĐT")
            return True

    except Exception:
        if expected == "success":
            print(f"[FAIL] TC{idx}: Thêm khách hàng KHÔNG thành công")
        elif expected == "missing":
            print(f"[FAIL] TC{idx}: Không hiển thị lỗi thiếu dữ liệu")
        elif expected == "invalid":
            print(f"[FAIL] TC{idx}: Không báo lỗi định dạng")
        elif expected == "duplicate_email":
            print(f"[FAIL] TC{idx}: Không phát hiện trùng email")
        elif expected == "duplicate_phone":
            print(f"[FAIL] TC{idx}: Không phát hiện trùng số điện thoại")

    return False

if __name__ == "__main__":
    driver = webdriver.Chrome(options=chrome_options)
    wait = WebDriverWait(driver, 10)

    passed = 0
    failed = 0

    try:
        login(driver, wait)

        for idx, tc in enumerate(TEST_CASES, start=1):
            result = run_test_case(driver, wait, *tc, idx)

            if result:
                passed += 1
            else:
                failed += 1

            time.sleep(DELAY)

        print("\n" + "="*50)
        print(f"TỔNG KẾT: {len(TEST_CASES)} test case")
        print(f" PASS: {passed}")
        print(f" FAIL: {failed}")
        print("="*50)

    finally:
        driver.quit()
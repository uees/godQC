"""
更新产品相关信息, 利用 http api
"""
import logging
import os

import requests

from report.common import get_products_form_xls
from settings import ROOT_PATH

logging.getLogger(__name__).setLevel(logging.WARNING)
logging.basicConfig(
    level=logging.DEBUG,
    format='%(asctime)s %(filename)s[line:%(lineno)d] %(levelname)s %(message)s',
    datefmt='%a, %d %b %Y %H:%M:%S',
    filename=f'{__name__}.log',
    filemode='w'
)


def login(host: str, email: str, password: str) -> str:
    response = requests.post(f'{host}/api/auth/login', data={
        'email': email, 'password': password
    }, headers={
        'X-Requested-With': 'XMLHttpRequest'
    })

    body = response.json()

    return body.get('token')


def get_product_by_name(host: str, token: str, product_name: str):
    response = requests.get(f'{host}/api/products', params={"internal_name": product_name}, headers={
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': f'Bearer {token}'
    })

    if response.status_code != 200:
        logging.error(f'发起请求 {host}/api/products 失败')
        return

    # {'data': list, 'total': 1}
    return response.json()


def update_product(host: str, token: str, id: int, data: dict):
    response = requests.post(f'{host}/api/products', data=data, headers={
        'X-Requested-With': 'XMLHttpRequest',
        'Authorization': f'Bearer {token}'
    })

    if response.status_code != 200:
        logging.error(f'更新失败, ID {id}')


if __name__ == "__main__":
    host = os.getenv('API_HOST')
    email = os.getenv('ADMIN_USER')
    passwd = os.getenv('ADMIN_PASS')
    token = login(host, email, passwd)

    database_filepath = os.path.join(ROOT_PATH, "basedata/database.xlsx")
    _, products = get_products_form_xls(database_filepath)
    for p in products:
        result = get_product_by_name(host, token, p["internal_name"])
        if not result or result["meta"]["total"] != 1:
            print(f"无效的产品{p['internal_name']}")
            continue

        product = result["data"][0]

        update_product(host, token, product["id"], {
            "label_viscosity": p["label_viscosity"],
            "viscosity_width": p["viscosity_width"]
        })
        print(f"更新了产品{p['internal_name']}")

# -- coding: utf-8 -*-

import os

from dotenv import load_dotenv

ROOT_PATH = os.path.abspath(os.path.dirname(__file__))

env_path = os.path.join(ROOT_PATH, '../.env')
load_dotenv(dotenv_path=env_path)

DB_CONNECTION = os.getenv('DB_CONNECTION')
DB_DATABASE = os.getenv('DB_DATABASE')
DB_HOST = os.getenv('DB_HOST')
DB_PORT = os.getenv('DB_PORT')
DB_USERNAME = os.getenv('DB_USERNAME')
DB_PASSWORD = os.getenv('DB_PASSWORD')

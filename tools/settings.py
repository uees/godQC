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

REDIS_HOST = os.getenv("REDIS_HOST")
REDIS_PORT = os.getenv("REDIS_PORT")
REDIS_PASSWORD = os.getenv("REDIS_PASSWORD")

REPORTS_ROOT = os.path.join(ROOT_PATH, '../storage/app/public/reports')
TEMPLATES_ROOT = os.path.join(ROOT_PATH, 'basedata/templates')
SPA_ROOT = os.path.join(ROOT_PATH, 'basedata/spa')

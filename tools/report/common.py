import os
from datetime import datetime

from settings import REPORTS_ROOT


def today_reports_root() -> str:
    """ 返回当日报告文件夹路径 """
    today_dir_name = datetime.strftime(datetime.now(), '%Y-%m-%d')
    today_path = os.path.join(REPORTS_ROOT, today_dir_name)
    if not os.path.exists(today_path):
        os.mkdir(today_path)
    return today_path


def unique_filepath(path: str, filename: str) -> str:
    """ 检查并生成唯一的文件名 """
    filepath = os.path.join(path, filename)

    if os.path.exists(filepath):
        shotname, extension = os.path.splitext(filename)
        filename = '%s_.%s' % (shotname, extension)

        return unique_filepath(path, filename)

    return filepath

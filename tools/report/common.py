# -*- coding:utf-8 -*-
import os
from datetime import datetime

from settings import REPORTS_ROOT


def today_reports_root():
    """ 返回当日报告文件夹路径 """
    today_dir_name = datetime.strftime(datetime.now(), '%Y-%m-%d')
    today_path = os.path.join(REPORTS_ROOT, today_dir_name)
    if not os.path.exists(today_path):
        os.mkdir(today_path)
    return today_path


def unique_filepath(path, filename):
    """ 检查并生成唯一的文件名 """
    shotname, extension = os.path.splitext(filename)
    filepath = os.path.join(path, '%s.%s' % (shotname, extension))

    if os.path.exists(filepath):
        filename = '%s(1).%s' % (shotname, extension)
        return unique_filepath(path, filename)

    return filepath

# -*- coding:utf-8 -*-
import os

from DictObject import DictObject

from .common import today_reports_root


class Generator(object):
    """ 检验报告生成器 """

    def __init__(self, product):
        self.reports_root = today_reports_root()
        self.product = product

    def generate_report(self):
        pass

    def report_filename(self):
        pass

    def unique_filepath(self, path, filename, ext):
        """ 检查并生成唯一的文件名 """
        filepath = '%s/%s.%s' % (path, filename, ext)
        if os.path.exists(filepath):
            filename = '%s(1)' % filename
            return self.unique_filename(path, filename, ext)

        return filepath

    def make_context(self):
        return DictObject()

    def get_templates(self):
        return []

    def get_template_path(self):
        pass

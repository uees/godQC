# -*- coding:utf-8 -*-
from celery.utils.log import get_task_logger

from .celery import app

logger = get_task_logger(__name__)


@app.task
def add(x, y):
    logger.info('Adding {0} + {1}'.format(x, y))
    return x + y


@app.task
def mul(x, y):
    return x * y


@app.task
def xsum(numbers):
    return sum(numbers)

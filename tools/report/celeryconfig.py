# -*- coding:utf-8 -*-

import settings

broker_url = "redis://{0.REDIS_HOST}:{0.REDIS_PORT}/0".format(settings)
result_backend = "redis://{0.REDIS_HOST}:{0.REDIS_PORT}/1".format(settings)
task_serializer = 'msgpack'  # 任务序列化和反序列化使用msgpack方案
result_serializer = 'json'   # 读取任务结果一般性能要求不高，所以使用了可读性更好的JSON
accept_content = ['json', 'msgpack']  # 指定接受的内容类型
timezone = 'Asia/Shanghai'
enable_utc = True

task_annotations = {
    # 'tasks.add': {'rate_limit': '10/m'}
}

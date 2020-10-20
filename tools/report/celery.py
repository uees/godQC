from celery import Celery

from report import celeryconfig

app = Celery('report', include=['report.tasks'])

app.config_from_object(celeryconfig)

if __name__ == '__main__':
    app.start()

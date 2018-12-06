# -- coding: utf-8 -*-

from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import scoped_session, sessionmaker

import settings

if settings.DB_CONNECTION == "sqlite":
    connString = "sqlite:///%s" % settings.DB_DATABASE
else:
    connString = "mysql+pymysql://%s:%s@%s:%s/%s" % (
        settings.DB_USERNAME, settings.DB_PASSWORD, settings.DB_HOST, settings.DB_PORT, settings.DB_DATABASE)

engine = create_engine(connString)

db_session = scoped_session(sessionmaker(autocommit=False,
                                         autoflush=False,
                                         bind=engine))

Base = declarative_base(bind=engine)

Base.query = db_session.query_property()

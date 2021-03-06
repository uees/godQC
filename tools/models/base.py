from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import scoped_session, sessionmaker

import settings

# only mysql
connString = "mysql+pymysql://{0.DB_USERNAME}:{0.DB_PASSWORD}@{0.DB_HOST}:{0.DB_PORT}/{0.DB_DATABASE}".format(settings)

engine = create_engine(connString)

db_session = scoped_session(sessionmaker(autocommit=False,
                                         autoflush=False,
                                         bind=engine))

Base = declarative_base(bind=engine)

Base.query = db_session.query_property()

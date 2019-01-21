# -- coding: utf-8 -*-
import json

from sqlalchemy import TIMESTAMP, Column, Integer, String, Text, text
from sqlalchemy.orm import relationship, synonym

from .base import Base


class Category(Base):

    __tablename__ = 'categories'

    __table_args__ = {
        "mysql_charset": "utf8mb4"
    }

    id = Column(Integer, primary_key=True, autoincrement=True)
    name = Column(String(64), unique=True)
    slug = Column(String(64))
    memo = Column(Text, nullable=True)
    _metas = Column(Text, nullable=True)  # report template
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    products = relationship("Product", back_populates="category")

    def _get_metas(self):
        return json.loads(self._metas)

    def _set_metas(self, metas):
        self._metas = json.dumps(metas)

    metas = synonym("_metas", descriptor=property(_get_metas, _set_metas))

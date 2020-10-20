import json

from sqlalchemy import (TIMESTAMP, Boolean, Column, ForeignKey, Integer,
                        SmallInteger, String, Text, UniqueConstraint, text)
from sqlalchemy.dialects.mysql import INTEGER
from sqlalchemy.orm import relationship, synonym

from .base import Base


class QCRecord(Base):

    __tablename__ = 'test_records'
    __table_args__ = {
        "mysql_charset": "utf8mb4"
    }

    id = Column(Integer, primary_key=True, autoincrement=True)
    product_batch_id = Column(INTEGER(unsigned=True), ForeignKey('product_batches.id'))
    test_times = Column(SmallInteger, default=1)
    conclusion = Column(String(64), nullable=True)
    testers = Column(String(256), nullable=True)
    completed_at = Column(TIMESTAMP(True), nullable=True)
    said_package_at = Column(TIMESTAMP(True), nullable=True)
    memo = Column(Text, nullable=True)
    show_reality = Column(Boolean, default=False)
    is_archived = Column(Boolean, default=False)
    is_created_doc = Column(Boolean, default=False)
    push_state = Column(String(128), default="no_push")
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    product_batch = relationship("ProductBatch", back_populates="test_records", lazy='joined')
    record_items = relationship("QCRecordItem", back_populates="test_record")


class QCRecordItem(Base):
    __tablename__ = 'test_record_items'
    __table_args__ = (
        UniqueConstraint("test_record_id", "item"),
        {
            "mysql_charset": "utf8mb4",
        }
    )

    id = Column(Integer, primary_key=True, autoincrement=True)
    test_record_id = Column(INTEGER(unsigned=True), ForeignKey('test_records.id'))
    item = Column(String(64))
    _spec = Column("spec", Text, nullable=True)
    value = Column(String(128), nullable=True)
    fake_value = Column(String(128), nullable=True)
    conclusion = Column(String(32), nullable=True)
    tester = Column(String(64), nullable=True)
    created_at = Column(TIMESTAMP(True), nullable=True, server_default=text('CURRENT_TIMESTAMP'))
    updated_at = Column(TIMESTAMP(True), nullable=True)

    test_record = relationship(QCRecord, back_populates="record_items")

    def _get_spec(self):
        if self._spec is not None:
            return json.loads(self._spec)

    def _set_spec(self, spec):
        self._spec = json.dumps(spec)

    spec = synonym("_spec", descriptor=property(_get_spec, _set_spec))

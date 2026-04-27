"""
db.py — MySQL connector for MaternaHealth
"""

import pymysql
import pymysql.cursors
from flask import g

DB_CONFIG = {
    'host':     'localhost',
    'user':     'root',         # change to your MySQL user
    'password': '',             # change to your MySQL password
    'db':       'maternal_dbase',
    'charset':  'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor,
}

def get_conn():
    try:
        if 'db' not in g:
            g.db = _open_conn()
        return g.db
    except RuntimeError:
        return _open_conn()

def _open_conn():
    return pymysql.connect(**DB_CONFIG)

def close_conn(e=None):
    db = g.pop('db', None)
    if db is not None:
        db.close()
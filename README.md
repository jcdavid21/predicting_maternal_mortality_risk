# Maternal Database System - Setup Guide

## Prerequisites
- XAMPP (Apache & MySQL)
- Python installed (Windows or macOS)

---

## Setup Instructions

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start:
  - Apache
  - MySQL

---

### 2. Create Database
- Open phpMyAdmin
- Create a new database named:

maternal_dbase

---

### 3. Import Database
- Locate the provided database file in the project folder
- Import it into `maternal_dbase` using phpMyAdmin

---

### 4. Setup Python Environment

#### Windows / macOS

Create a virtual environment:
```
python -m venv venv
```

Activate virtual environment:

Windows:
```
venv\Scripts\activate
```

macOS:
```
source venv/bin/activate
```

---

### 5. Install Dependencies
```
pip install -r requirements.txt
```

---

### 6. Run the Application
```
python app.py
```

---

## Dataset Information

The dataset used for training the model:

MMR-maternal-deaths-and-LTR_MMEIG-trends_2000-2017_Revised-2021.xlsx

This dataset is used for machine learning and predictive analytics within the system.

---

## Notes
- Ensure MySQL is running before launching the application
- Make sure the database connection settings in the project match your local setup

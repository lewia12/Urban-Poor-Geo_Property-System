from flask import Flask, request, redirect, url_for
import sqlite3

app = Flask(__name__)

def init_db():
    conn = sqlite3.connect('certificates.db')
    c = conn.cursor()
    c.execute('''
        CREATE TABLE IF NOT EXISTS certificates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            certificate_number TEXT NOT NULL,
            recipient_name TEXT NOT NULL,
            issue_date TEXT NOT NULL,
            expiry_date TEXT NOT NULL,
            certificate_type TEXT NOT NULL
        )
    ''')
    conn.commit()
    conn.close()

@app.route('/')
def index():
    return redirect(url_for('form'))

@app.route('/form')
def form():
    return app.send_static_file('form.html')

@app.route('/submit', methods=['POST'])
def submit():
    certificate_number = request.form['certificateNumber']
    recipient_name = request.form['recipientName']
    issue_date = request.form['issueDate']
    expiry_date = request.form['expiryDate']
    certificate_type = request.form['certificateType']

    conn = sqlite3.connect('certificates.db')
    c = conn.cursor()
    c.execute('''
        INSERT INTO certificates (certificate_number, recipient_name, issue_date, expiry_date, certificate_type)
        VALUES (?, ?, ?, ?, ?)
    ''', (certificate_number, recipient_name, issue_date, expiry_date, certificate_type))
    conn.commit()
    conn.close()

    return 'Certificate submitted successfully!'

if __name__ == '__main__':
    init_db()
    app.run(debug=True)

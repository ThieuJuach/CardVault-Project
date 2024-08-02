A)	Introduction 
This project is on a credit card vault which a merchant uses to store customer’s credit card details. The credit card information is collected from a customer once, then the details are stored in the vault. The next time the merchant wants to invoice a customer, you can use the stored card information. There are three main users to this web application which include the merchant, the teller and the customer. Each of the three have their own different CRUD rights based on the sensitivity of data. 

B)	Type of Information
This section covers the type of information that the system will be dealing with. The information types include:
1.	Confidential Information – Passwords, and Credit card details
2.	Sensitive Information – Credit card details
3.	Public Information – Customer details, Product details, and Transaction ID

C)	User Access Levels
This section covers the user access levels of information that the system will be dealing with. The information user access levels are assigned as follows:
1.	Confidential Information:
a.	Employee – Select, Insert, Update 
b.	Admin – Select, Insert, Update, Delete
c.	Customer – Select, Insert, Update
2.	Sensitive Information: 
a.	Employee: Select, Update
b.	Admin: Select, Insert, Update, Delete
c.	Customer: Select, Insert, Update 
3.	Public Information: 
a.	Employee & Admin: Select, Insert, Update, Delete
b.	Customer: Select

D)	Relationship Schema 
This section covers the relationship schemas of the tables that are used to store the information that the system will be dealing with. The tables relationship schemas and structure are shown below:
•	credit_card(card_id (PK), user_id (FK), card_number, cvv, expiry_date, card_balance) 
•	transactions(transaction_id (PK), card_id (FK), user_id (FK), description, amount)
•	users(user_id (PK), user_name, role, email_address, password)

The users table is the main table which will be storing the users details and encrypted password. The user id attribute has been used to link a credit card to a customer in the credit card table. The users id and the card id has been used in the transactions table to link a transaction to a customer and to receive money from the card. 


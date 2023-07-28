-- Create patient table
CREATE TABLE IF NOT EXISTS patient (
	patientId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255) NOT NULL,
	phoneNumber VARCHAR(20) NOT NULL,
	ssn VARCHAR(20) NOT NULL,
	gender ENUM('Male', 'Female', 'Other') NOT NULL,
	imageUrl VARCHAR(255),
	dateOfBirth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	passwordHash VARCHAR(255) NOT NULL
);

-- Create doctor table
CREATE TABLE IF NOT EXISTS doctor (
	doctorId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	gender ENUM('Male', 'Female', 'Other') NOT NULL,
	phoneNumber VARCHAR(20) NOT NULL,
	email VARCHAR(255) NOT NULL,
	imageUrl VARCHAR(255),
	medicalCertificateNumber VARCHAR(50),
	specialization VARCHAR(255),
	passwordHash VARCHAR(255) NOT NULL
);

-- Create patient_doctor table
CREATE TABLE IF NOT EXISTS patient_doctor (
	patientDoctorid INT AUTO_INCREMENT PRIMARY KEY,
	patientId INT NOT NULL,
	doctorId INT NOT NULL,
	isPrimary BOOLEAN,
	FOREIGN KEY (patientId) REFERENCES patient(patientId),
	FOREIGN KEY (doctorId) REFERENCES doctor(doctorId)
);

-- Create pharmacy table
CREATE TABLE IF NOT EXISTS pharmacy (
	pharmacyId INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	location VARCHAR(255),
	email VARCHAR(255),
	phoneNumber VARCHAR(20)
);

-- Create pharmaceutical table
CREATE TABLE IF NOT EXISTS pharmaceutical (
	pharmaceuticalId INT AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(255) NOT NULL,
	location VARCHAR(255),
	email VARCHAR(255),
	phoneNumber VARCHAR(20)
);

-- Create pharmacist table
CREATE TABLE IF NOT EXISTS pharmacist (
	pharmacistId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255),
	phoneNumber VARCHAR(20),
	imageUrl VARCHAR(255),
	passwordHash VARCHAR(255) NOT NULL,
	pharmacyId INT NOT NULL,
	FOREIGN KEY (pharmacyId) REFERENCES pharmacy(pharmacyId)
);

-- Create supervisor table
CREATE TABLE IF NOT EXISTS supervisor (
	supervisorId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	email VARCHAR(255),
	phoneNumber VARCHAR(20),
	imageUrl VARCHAR(255),
	gender ENUM('Male', 'Female', 'Other'),
        passwordHash VARCHAR(255) NOT NULL,
	pharmaceuticalId INT NOT NULL,
	FOREIGN KEY (pharmaceuticalId) REFERENCES pharmaceutical(pharmaceuticalId)
);

-- Create contract table
CREATE TABLE IF NOT EXISTS contract (
	contractId INT AUTO_INCREMENT PRIMARY KEY,
	dateCreated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	lastUpdated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	startDate DATE,
	endDate DATE,
	pharmacyId INT NOT NULL,
	pharmaceuticalId INT NOT NULL,
	FOREIGN KEY (pharmacyId) REFERENCES pharmacy(pharmacyId),
	FOREIGN KEY (pharmaceuticalId) REFERENCES pharmaceutical(pharmaceuticalId)
);

-- Create drug table
CREATE TABLE IF NOT EXISTS drug (
	drugId INT AUTO_INCREMENT PRIMARY KEY,
	scientificName VARCHAR(255) NOT NULL,
	commonName VARCHAR(255),
	formula VARCHAR(255),
	amount DECIMAL(10, 2),
	form VARCHAR(100),
	expiryDate DATE,
	manufacturingDate DATE,
	contractId INT NOT NULL,
	FOREIGN KEY (contractId) REFERENCES contract(contractId)
);

-- Create prescription table
CREATE TABLE IF NOT EXISTS prescription (
	prescriptionId INT AUTO_INCREMENT PRIMARY KEY,
	drugId INT NOT NULL,
	patientDoctorId INT NOT NULL,
	dosage VARCHAR(100),
	frequency VARCHAR(100),
	startDate DATE,
	endDate DATE,
	price DECIMAL(10, 2),
	isGiven BOOLEAN,
	FOREIGN KEY (drugId) REFERENCES drug(drugId),
	FOREIGN KEY (patientDoctorId) REFERENCES patient_doctor(patientDoctorid)
);

CREATE TABLE IF NOT EXISTS administrator (
	administratorId INT AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(100) NOT NULL,
	email VARCHAR(100) NOT NULL UNIQUE,
	phoneNumber VARCHAR(20),
	imageUrl VARCHAR(255),
	passwordHash VARCHAR(255) NOT NULL
);

INSERT INTO administrator (name, email, phoneNumber, imageUrl, passwordHash) VALUES ('Jomo Kenyatta Wairegi', 'administrator@admin.com', '+254 777 888 999', 'administrator.jpg', '$2y$10$g0Boo9CvgJeQ7lHf14g6vuYwniyF7/Nds.DZepXd/v6Sc0dClybbK');
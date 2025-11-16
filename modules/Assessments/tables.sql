CREATE TABLE assessments_types (
    ID serial PRIMARY KEY,
    TITLE varchar(255) NOT NULL,
    SHORT_NAME varchar(50),
    SORT_ORDER int DEFAULT 0
);

CREATE TABLE assessments_scores (
    ID serial PRIMARY KEY,
    STUDENT_ID int NOT NULL REFERENCES students(student_id),
    ASSESSMENT_TYPE_ID int NOT NULL REFERENCES assessments_types(ID),
    SCORE numeric(10,2),
    DATE_TAKEN date,
    COMMENTS text
);

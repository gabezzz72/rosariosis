--
-- SQL commands to install the Assessments module
-- Creates two new tables:
-- 1. assessments: Stores the assessment types (e.g., PSAT, PACT)
-- 2. assessment_scores: Stores the scores for each student
--

-- Table for assessment types
CREATE TABLE IF NOT EXISTS assessments (
    ASSESSMENT_ID SERIAL PRIMARY KEY,
    SCHOOL_ID INT NOT NULL,
    SYEAR INT NOT NULL,
    TITLE VARCHAR(100) NOT NULL
);

-- Table for student scores
CREATE TABLE IF NOT EXISTS assessment_scores (
    SCORE_ID SERIAL PRIMARY KEY,
    STUDENT_ID INT NOT NULL,
    ASSESSMENT_ID INT NOT NULL,
    SCORE_DATE DATE NOT NULL,
    SCORE VARCHAR(20) NOT NULL,
    FOREIGN KEY (ASSESSMENT_ID) REFERENCES assessments(ASSESSMENT_ID) ON DELETE CASCADE
);

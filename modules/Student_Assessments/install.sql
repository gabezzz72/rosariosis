CREATE TABLE student_assessments_types (
    assessment_type_id SERIAL PRIMARY KEY,
    syear VARCHAR(4) NOT NULL,
    school_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    title VARCHAR(100) NOT NULL,
    sort_order INT
);

CREATE TABLE student_assessments_scores (
    assessment_score_id SERIAL PRIMARY KEY,
    student_id INT NOT NULL,
    assessment_type_id INT NOT NULL REFERENCES student_assessments_types(assessment_type_id) ON DELETE CASCADE,
    syear VARCHAR(4) NOT NULL,
    assessment_date DATE,
    score VARCHAR(100),
    comment TEXT
);

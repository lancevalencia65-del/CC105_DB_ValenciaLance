System Overview and Purpose

The Student Information System is designed to manage and organize academic records efficiently. Its main purpose is to store information about students, teachers, courses, and enrollments in a structured database. The system helps reduce data redundancy, improves data accuracy, and allows users to retrieve meaningful academic information through SQL queries. It is suitable for small to medium-sized educational institutions that require a simple and organized record-keeping system.

Table Descriptions and Relationships
Students Table

This table stores the basic information of students.
Each student is uniquely identified using a student ID.

Key Fields:

student_id (Primary Key)

first_name

last_name

email

section

Teachers Table

This table contains information about teachers who handle courses.

Key Fields:

teacher_id (Primary Key)

full_name

email

Courses Table

This table stores details about the courses offered by the institution.

Key Fields:

course_id (Primary Key)

course_name

units

Enrollments Table

This table serves as the linking table that connects students, teachers, and courses. It represents which student is enrolled in which course and handled by which teacher.

Key Fields:

enrollment_id (Primary Key)

student_id (Foreign Key)

course_id (Foreign Key)

teacher_id (Foreign Key)

semester

Table Relationships

One student can enroll in multiple courses (One-to-Many relationship).

One course can have many enrolled students (One-to-Many relationship).

One teacher can handle multiple courses or enrollments.

The enrollments table establishes the relationships among students, teachers, and courses using foreign keys.

Sample Outputs or Screenshots of Query Results
Sample Query 1: List of Students and Their Enrolled Courses

Output Description:
This query displays the student’s name together with the courses they are enrolled in.

First Name	Last Name	Course Name
Juan	Dela Cruz	Database Systems
Maria	Santos	Web Development
Sample Query 2: Teachers Assigned to Courses

Output Description:
This query shows which teacher is assigned to a specific course.

Teacher Name	Course Name
Ana Reyes	Database Systems
Carlos Lopez	Web Development

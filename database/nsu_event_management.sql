-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2024 at 11:19 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nsu_event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `adminID` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `lastLogin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`adminID`, `position`, `department`, `lastLogin`) VALUES
(7, 'IT Project Manager', 'IT', '2024-06-18 07:28:46'),
(18, 'Chair', 'ECE', '2024-06-19 13:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `attendeeID` int(11) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`attendeeID`, `type`) VALUES
(9, 'Student'),
(10, 'Faculty'),
(11, 'Student'),
(12, 'Student'),
(13, 'Student'),
(14, 'Student'),
(15, 'Student'),
(16, 'Student'),
(17, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `eventattendees`
--

CREATE TABLE `eventattendees` (
  `eventID` int(11) NOT NULL,
  `attendeeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventattendees`
--

INSERT INTO `eventattendees` (`eventID`, `attendeeID`) VALUES
(6, 11),
(7, 10);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int(11) NOT NULL,
  `eventName` varchar(255) DEFAULT NULL,
  `eventDetails` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `duration` float DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `bannerURL` varchar(255) DEFAULT NULL,
  `registrationDeadline` date DEFAULT NULL,
  `fee` double DEFAULT NULL,
  `sponsor` varchar(255) DEFAULT NULL,
  `organizerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `eventName`, `eventDetails`, `date`, `time`, `duration`, `location`, `status`, `bannerURL`, `registrationDeadline`, `fee`, `sponsor`, `organizerID`) VALUES
(4, 'NSU Orientation', 'Orientation for new students at NSU', '2024-08-15', '10:00:00', 3.5, 'NSU Auditorium', 'approved', 'images/uploads/eventBanners/orientation.jpg', '2024-08-10', 0, 'NSU Administration', 8),
(5, 'NSU Career Fair', 'Annual career fair with companies', '2024-09-01', '09:00:00', 8, 'NSU Sports Complex', 'approved', 'images/uploads/eventBanners/career_fair.jpg', '2024-08-25', 0, 'NSU Career Services', 8),
(6, 'Health Awareness Seminar', 'Seminar on health and wellness', '2024-07-20', '11:00:00', 2, 'NSU Health Center', 'approved', 'images/uploads/eventBanners/health_seminar.jpg', '2024-07-15', 0, 'NSU Health Department', 8),
(7, 'Art Exhibition', 'Exhibition of student artwork', '2024-06-30', '14:00:00', 4, 'NSU Art Gallery', 'approved', 'images/uploads/eventBanners/art_exhibition.jpg', '2024-06-25', 0, 'NSU Art Club', 8),
(8, 'Environmental Workshop', 'Workshop on environmental conservation', '2024-08-10', '13:00:00', 3, 'NSU Conference Room B', 'approved', 'images/uploads/eventBanners/environmental_workshop.jpg', '2024-08-05', 5, 'NSU Green Club', 8),
(9, 'Music Concert', 'Concert by NSU band', '2024-09-15', '18:00:00', 3, 'NSU Open Grounds', 'approved', 'images/uploads/eventBanners/music_concert.jpg', '2024-09-10', 10, 'NSU Music Club', 8),
(10, 'Robotics Competition', 'Competition for robotics enthusiasts', '2024-10-25', '09:00:00', 6, 'NSU Tech Lab', 'approved', 'images/uploads/eventBanners/robotics_competition.jpg', '2024-10-20', 15, 'NSU Robotics Club', 8),
(11, 'Tech Symposium', 'Symposium on emerging technologies', '2024-11-05', '09:30:00', 7, 'NSU Engineering Hall', 'approved', 'images/uploads/eventBanners/tech_symposium.jpg', '2024-10-28', 10, 'NSU Tech Club', 8),
(12, 'Cultural Festival', 'Festival showcasing various cultures', '2024-06-20', '12:00:00', 5, 'NSU Open Ground', 'approved', 'images/uploads/eventBanners/cultural_fest.jpg', '2024-06-18', 5, 'NSU Cultural Committee', 8),
(14, 'SOP writing', 'A seminar for writing sop for forieng education', '2024-06-25', '14:00:00', 1, 'LIB600', 'approved', 'images/uploads/eventBanners/Capture-3.jpg', '2024-06-24', 0, 'None', 8);

-- --------------------------------------------------------

--
-- Table structure for table `eventvolunteers`
--

CREATE TABLE `eventvolunteers` (
  `eventID` int(11) NOT NULL,
  `volunteerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `attendeeID` int(11) NOT NULL,
  `facultyInitial` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`attendeeID`, `facultyInitial`) VALUES
(10, 'F1');

-- --------------------------------------------------------

--
-- Table structure for table `generalevents`
--

CREATE TABLE `generalevents` (
  `eventID` int(11) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `organizer` varchar(255) DEFAULT NULL,
  `expectedAttendance` int(11) DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `hasMultipleSessions` tinyint(1) DEFAULT NULL,
  `equipmentNeeded` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `generalevents`
--

INSERT INTO `generalevents` (`eventID`, `type`, `organizer`, `expectedAttendance`, `theme`, `hasMultipleSessions`, `equipmentNeeded`) VALUES
(4, 'Orientation', 'NSU Administration', 1000, 'New Student Orientation', 0, 'Projector, Microphone, Seating Arrangement'),
(5, 'Career Event', 'NSU Career Services', 500, 'Job Fair', 1, 'Booths, Banners, Registration Desk'),
(7, 'Art Event', 'NSU Art Club', 150, 'Art Exhibition', 0, 'Art Supplies, Display Boards'),
(9, 'Music Event', 'NSU Music Club', 250, 'Live Concert', 0, 'Sound System, Instruments, Lighting'),
(12, 'Cultural Event', 'NSU Cultural Committee', 800, 'Multi-Cultural Festival', 1, 'Stages, Sound System, Lights');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationID` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `senderID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `organizerID` int(11) NOT NULL,
  `organizationType` varchar(255) NOT NULL,
  `organizationName` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizers`
--

INSERT INTO `organizers` (`organizerID`, `organizationType`, `organizationName`, `position`, `contactNumber`, `address`) VALUES
(8, 'Club', 'NSU Pharmacy', 'President', '1212', 'sdjfaklsdjfkl');

-- --------------------------------------------------------

--
-- Table structure for table `seminars`
--

CREATE TABLE `seminars` (
  `eventID` int(11) NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `speakers` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seminars`
--

INSERT INTO `seminars` (`eventID`, `topic`, `speakers`) VALUES
(6, 'Health and Wellness', 'Dr. Karen White, Dr. Peter Brown'),
(8, 'Environmental Conservation Strategies', 'Dr. Laura Blue, Mr. Kevin White'),
(10, 'Robotics in Modern Industry', 'Dr. Henry Silver, Mr. David Gold'),
(11, 'Future of AI and Robotics', 'Dr. Mark Johnson, Prof. Lisa King'),
(14, 'Study of purpose writing', 'Dr. Khalid Hossain');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `attendeeID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`attendeeID`, `studentID`) VALUES
(16, 2001000),
(9, 2012017),
(17, 2012177),
(11, 2012910);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photoURL` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `email`, `photoURL`) VALUES
(7, 'a1', '$2y$10$YbtEeZccOiLw9Tda6ctXhuoo0h1g3PiQjwFIBv.hDDXmg9SY438PG', 'a1@gmail.com', 'images/uploads/adminPhoto/FjU2lkcWYAgNG6d.jpg'),
(8, 'o1', '$2y$10$VnoDrVInuOv2mGv674skzOOpIDOTvrD2NCZtjxCmOKTyXoDgQdfbO', 'o1@gmail.com', 'images/uploads/organizerPhoto/caio-fernandes-1288b8b9-187d-4812-a73f-1dd14c9bb8d3.jpg'),
(9, 's1', '$2y$10$8qVA56Qn2gmCMlD9j5pDhOVDf1gWIMI4dToy8IgWc30y5DK3IY5YS', 's1@gmail.com', 'images/uploads/users/Screenshot 2024-06-19 111455.png'),
(10, 'f1', '$2y$10$5rL0/vP7eVzJbcAdElpgau8v1ZZ3dAwlWmD/u95NUEFoslwHuGQF6', 'f1@gmail.com', 'images/uploads/users/Screenshot 2024-06-19 111931.png'),
(11, 's2', '$2y$10$OHZ6JuJYQnB3aCxgKC/UReHzr.eEWOaiofirRKkbN.KZjE2LNYagG', 's2@gmail.com', 'images/uploads/users/Screenshot 2024-06-19 113737.png'),
(16, 's3', '$2y$10$qCDsLWnMYvDY/T3matsSSOMXV8tUszZk74j8Vc0goLl7DNTl0VR.y', 's3@gmail.com', 'images/uploads/users/Screenshot 2024-06-19 165330.png'),
(17, 's4', '$2y$10$slgWxVjb0KYwiYesfnQbcue3uWDu24qTGQDpFX2RT2W0EhcNg5gjq', 's4@gmail.com', 'images/uploads/users/Nourash Azmine Chowdhury.png'),
(18, 'a4', '$2y$10$I6BiYfwD1BahGFhLfmf5SOGTz5ll0.s6LX7xVs0ehwj.3nVwMG6yO', 'a4@gmail.com', 'images/uploads/adminPhoto/Screenshot 2024-06-19 111931.png');

-- --------------------------------------------------------

--
-- Table structure for table `volunteers`
--

CREATE TABLE `volunteers` (
  `volunteerID` int(11) NOT NULL,
  `organizerID` int(11) NOT NULL,
  `assignedTasks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteers`
--

INSERT INTO `volunteers` (`volunteerID`, `organizerID`, `assignedTasks`) VALUES
(9, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`attendeeID`);

--
-- Indexes for table `eventattendees`
--
ALTER TABLE `eventattendees`
  ADD PRIMARY KEY (`eventID`,`attendeeID`),
  ADD KEY `attendeeID` (`attendeeID`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `fk_organizer` (`organizerID`);

--
-- Indexes for table `eventvolunteers`
--
ALTER TABLE `eventvolunteers`
  ADD PRIMARY KEY (`eventID`,`volunteerID`),
  ADD KEY `volunteerID` (`volunteerID`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`attendeeID`),
  ADD UNIQUE KEY `facultyInitial` (`facultyInitial`);

--
-- Indexes for table `generalevents`
--
ALTER TABLE `generalevents`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationID`),
  ADD KEY `eventID` (`eventID`),
  ADD KEY `senderID` (`senderID`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`organizerID`);

--
-- Indexes for table `seminars`
--
ALTER TABLE `seminars`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`attendeeID`),
  ADD UNIQUE KEY `studentID` (`studentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD PRIMARY KEY (`volunteerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `attendeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `volunteers`
--
ALTER TABLE `volunteers`
  MODIFY `volunteerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`adminID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `eventattendees`
--
ALTER TABLE `eventattendees`
  ADD CONSTRAINT `eventattendees_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`),
  ADD CONSTRAINT `eventattendees_ibfk_2` FOREIGN KEY (`attendeeID`) REFERENCES `attendees` (`attendeeID`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_organizer` FOREIGN KEY (`organizerID`) REFERENCES `organizers` (`organizerID`);

--
-- Constraints for table `eventvolunteers`
--
ALTER TABLE `eventvolunteers`
  ADD CONSTRAINT `eventvolunteers_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`),
  ADD CONSTRAINT `eventvolunteers_ibfk_2` FOREIGN KEY (`volunteerID`) REFERENCES `volunteers` (`volunteerID`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`attendeeID`) REFERENCES `attendees` (`attendeeID`);

--
-- Constraints for table `generalevents`
--
ALTER TABLE `generalevents`
  ADD CONSTRAINT `generalevents_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`senderID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `organizers`
--
ALTER TABLE `organizers`
  ADD CONSTRAINT `organizers_ibfk_1` FOREIGN KEY (`organizerID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `seminars`
--
ALTER TABLE `seminars`
  ADD CONSTRAINT `seminars_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`attendeeID`) REFERENCES `attendees` (`attendeeID`);

--
-- Constraints for table `volunteers`
--
ALTER TABLE `volunteers`
  ADD CONSTRAINT `volunteers_ibfk_1` FOREIGN KEY (`volunteerID`) REFERENCES `users` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Table structure for table `tbl_stack_type`
--

DROP TABLE IF EXISTS `tbl_stack_type`;
CREATE TABLE `tbl_stack_type` (
  `stack_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `stack_type_name` varchar(255) DEFAULT NULL,
  `stack_type_desc` varchar(255) NOT NULL,
  `catalogue_pattern` varchar(255) NOT NULL,
  `priority` int(3) NOT NULL,
  PRIMARY KEY (`stack_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_item_type`
--

DROP TABLE IF EXISTS `tbl_item_type`;
CREATE TABLE `tbl_item_type` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `type_desc` varchar(256) NOT NULL,
  `is_stack` tinyint(1) NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_buildings`
--

DROP TABLE IF EXISTS `tbl_buildings`;
CREATE TABLE `tbl_buildings` (
  `bldg_id` int(11) NOT NULL AUTO_INCREMENT,
  `bldg_name` varchar(255) NOT NULL,
  `bldg_desc` varchar(255) NOT NULL,
  PRIMARY KEY (`bldg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_floors`
--

DROP TABLE IF EXISTS `tbl_floors`;
CREATE TABLE `tbl_floors` (
  `floor_id` int(11) NOT NULL AUTO_INCREMENT,
  `img_url` varchar(255) CHARACTER SET utf8 NOT NULL,
  `floor_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `bldg_id` int(11) NOT NULL,
  `weight` int(3) NOT NULL,
  PRIMARY KEY (`floor_id`),
  KEY `bldg_id` (`bldg_id`),
  CONSTRAINT `tbl_floors_ibfk_1` FOREIGN KEY (`bldg_id`) REFERENCES `tbl_buildings` (`bldg_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_log`
--

DROP TABLE IF EXISTS `tbl_log`;
CREATE TABLE `tbl_log` (
  `ip_address` varchar(15) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(50) NOT NULL,
  `query` varchar(255) NOT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `is_mobile` tinyint(4) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_admin`
--

DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `matrix_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(127) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_location`
--

DROP TABLE IF EXISTS `tbl_location`;
CREATE TABLE `tbl_location` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_name` varchar(255) NOT NULL,
  `floor_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `map_x` int(11) NOT NULL,
  `map_y` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`loc_id`),
  KEY `floor_id` (`floor_id`),
  KEY `type_id` (`type_id`),
  CONSTRAINT `tbl_location_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `tbl_floors` (`floor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_location_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `tbl_item_type` (`type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tbl_stack_range`
--

DROP TABLE IF EXISTS `tbl_stack_range`;
CREATE TABLE `tbl_stack_range` (
  `loc_id` int(11) NOT NULL,
  `stack_type_id` int(11) NOT NULL,
  `call_range_start` varchar(255) NOT NULL,
  `call_range_end` varchar(255) NOT NULL,
  `call_int_start` varchar(32) NOT NULL,
  `call_int_end` varchar(32) NOT NULL,
  KEY `loc_id` (`loc_id`),
  KEY `stack_type_id` (`stack_type_id`),
  CONSTRAINT `tbl_stack_range_ibfk_1` FOREIGN KEY (`loc_id`) REFERENCES `tbl_location` (`loc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_stack_range_ibfk_2` FOREIGN KEY (`stack_type_id`) REFERENCES `tbl_stack_type` (`stack_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Username: admin
-- Password: admin
INSERT INTO `tbl_admin` VALUES (0,'admin','99f390e3465ad07602a885ef9fa35913','Super Admin');



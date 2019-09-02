CREATE SCHEMA `basket` DEFAULT CHARACTER SET utf8 ;
USE `basket`;
CREATE TABLE basket
(
    basket_id      char(36)           NOT NULL,
    warehouse      VARCHAR(100)       NOT NULL,
    basket_content LONGTEXT           NOT NULL,
    date_at        datetime           NOT NULL,
    INDEX warehouse_idx (warehouse),
    PRIMARY KEY (basket_id)
);
CREATE TABLE tx_instagram_feed (
    uid int(11) NOT NULL auto_increment,
    import_date int(11) unsigned DEFAULT '0' NOT NULL,
    username varchar(255) DEFAULT '' NOT NULL,
    data mediumtext NOT NULL,

    PRIMARY KEY (uid)
);

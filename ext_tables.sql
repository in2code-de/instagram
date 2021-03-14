CREATE TABLE tx_instagram_feed (
    uid int(11) NOT NULL auto_increment,
    import_date int(11) unsigned DEFAULT '0' NOT NULL,
    username varchar(255) DEFAULT '' NOT NULL,
    data mediumtext NOT NULL,

    PRIMARY KEY (uid)
);

CREATE TABLE tx_instagram_token (
    uid int(11) NOT NULL auto_increment,
    username varchar(255) DEFAULT '' NOT NULL,
    user_id varchar(255) DEFAULT '' NOT NULL,
    token varchar(255) DEFAULT '' NOT NULL,
    expire_date int(11) unsigned DEFAULT '0' NOT NULL,
    app_id varchar(255) DEFAULT '' NOT NULL,
    app_secret varchar(255) DEFAULT '' NOT NULL,
    app_return_url varchar(255) DEFAULT '' NOT NULL,
    crdate int(11) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid)
);

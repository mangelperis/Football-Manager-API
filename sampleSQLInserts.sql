/* CLUBS */
INSERT INTO clubs (name, shortname, country, budget, email, created, updated)
VALUES
    ('Manchester United', 'MUF', 'EN', 500000000, 'info@manutd.com', NOW(), NOW()),
    ('Real Madrid', 'RMF', 'ES', 600000000, 'info@realmadrid.com', NOW(), NOW()),
    ('Bayern Munich', 'FCB', 'GE', 450000000, 'info@fcbayern.com', NOW(), NOW()),
    ('Paris Saint-Germain', 'PSG', 'FR', 550000000, 'info@psg.fr', NOW(), NOW()),
    ('Juventus', 'JUV', 'IT', 400000000, 'info@juventus.com', NOW(), NOW());

/* PLAYERS */
INSERT INTO players (name, position, club_id, salary, email, created, updated)
VALUES
    ('Cristiano Ronaldo', 'F', 1, 500000, 'cr7@manutd.com', NOW(), NOW()),
    ('Bruno Fernandes', 'CM', 1, 250000, 'bruno@manutd.com', NOW(), NOW()),
    ('Marcus Rashford', 'F', 1, 200000, 'rashford@manutd.com', NOW(), NOW()),
    ('David de Gea', 'GK', 1, 375000, 'degea@manutd.com', NOW(), NOW()),
    ('Harry Maguire', 'D', 1, 190000, 'maguire@manutd.com', NOW(), NOW()),

    ('Karim Benzema', 'F', 2, 400000, 'benzema@realmadrid.com', NOW(), NOW()),
    ('Luka Modric', 'CM', 2, 300000, 'modric@realmadrid.com', NOW(), NOW()),
    ('Thibaut Courtois', 'GK', 2, 200000, 'courtois@realmadrid.com', NOW(), NOW()),
    ('Sergio Ramos', 'D', 2, 250000, 'ramos@realmadrid.com', NOW(), NOW()),
    ('Toni Kroos', 'CM', 2, 350000, 'kroos@realmadrid.com', NOW(), NOW()),

    ('Robert Lewandowski', 'F', 3, 450000, 'lewy@fcbayern.com', NOW(), NOW()),
    ('Thomas Müller', 'F', 3, 300000, 'muller@fcbayern.com', NOW(), NOW()),
    ('Manuel Neuer', 'GK', 3, 200000, 'neuer@fcbayern.com', NOW(), NOW()),
    ('Joshua Kimmich', 'CM', 3, 250000, 'kimmich@fcbayern.com', NOW(), NOW()),
    ('Leroy Sané', 'F', 3, 350000, 'sane@fcbayern.com', NOW(), NOW()),

    ('Kylian Mbappé', 'F', 4, 600000, 'mbappe@psg.fr', NOW(), NOW()),
    ('Neymar Jr', 'F', 4, 700000, 'neymar@psg.fr', NOW(), NOW()),
    ('Ángel Di María', 'CM', 4, 280000, 'dimaria@psg.fr', NOW(), NOW()),
    ('Keylor Navas', 'GK', 4, 180000, 'navas@psg.fr', NOW(), NOW()),
    ('Marquinhos', 'D', 4, 220000, 'marquinhos@psg.fr', NOW(), NOW()),

    ('Cristiano Ronaldo', 'F', 5, 600000, 'cr7@juventus.com', NOW(), NOW()),
    ('Paulo Dybala', 'F', 5, 400000, 'dybala@juventus.com', NOW(), NOW()),
    ('Matthijs de Ligt', 'D', 5, 200000, 'deligt@juventus.com', NOW(), NOW()),
    ('Wojciech Szczęsny', 'GK', 5, 150000, 'szczesny@juventus.com', NOW(), NOW()),
    ('Federico Chiesa', 'F', 5, 250000, 'chiesa@juventus.com', NOW(), NOW());


/* COACHES */
INSERT INTO coaches (name, role, salary, email, club_id, created, updated)
VALUES
    ('Erik ten Hag', 'Head', 500000, 'erik.tenhag@manutd.com', 1, NOW(), NOW()),
    ('Carlo Ancelotti', 'Head', 600000, 'carlo.ancelotti@realmadrid.com', 2, NOW(), NOW()),
    ('Thomas Tuchel', 'Head', 550000, 'thomas.tuchel@fcbayern.com', 3, NOW(), NOW()),
    ('Christophe Galtier', 'Head', 450000, 'christophe.galtier@psg.fr', 4, NOW(), NOW()),
    ('Massimiliano Allegri', 'Head', 500000, 'massimiliano.allegri@juventus.com', 5, NOW(), NOW()),
    ('Mitchell van der Gaag', 'Assistant', 200000, 'mitchell.vandergaag@manutd.com', 1, NOW(), NOW()),
    ('Davide Ancelotti', 'Assistant', 250000, 'davide.ancelotti@realmadrid.com', 2, NOW(), NOW()),
    ('Dino Toppmöller', 'Assistant', 220000, 'dino.toppmoller@fcbayern.com', 3, NOW(), NOW()),
    ('Zsolt Löw', 'Assistant', 180000, 'zsolt.low@psg.fr', 4, NOW(), NOW()),
    ('Marco Landucci', 'Assistant', 200000, 'marco.landucci@juventus.com', 5, NOW(), NOW());


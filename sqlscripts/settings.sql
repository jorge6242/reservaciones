-- ALTER TABLE settings ADD bookingUser_maxTimePerDay INT NULL;
-- ALTER TABLE settings ADD bookingUser_maxTimePerWeek INT NULL;
-- ALTER TABLE settings ADD bookingUser_maxTimePerMonth INT NULL;

-- ALTER TABLE settings ADD bookingGuest_maxTimePerDay INT NULL;
-- ALTER TABLE settings ADD bookingGuest_maxTimePerWeek INT NULL;
-- ALTER TABLE settings ADD bookingGuest_maxTimePerMonth INT NULL;


ALTER TABLE settings ADD bookingUserPerWeek INT NULL;
ALTER TABLE settings ADD bookingUserPerMonth INT NULL;
ALTER TABLE settings ADD bookingGuestPerDay INT NULL;
ALTER TABLE settings ADD bookingGuestPerWeek INT NULL;
ALTER TABLE settings ADD bookingGuestPerMonth INT NULL;
ALTER TABLE settings ADD bookingUser_MinGuests INT NULL;

2025-03-28
==========

Added
-----

- Option to re-use CURL blocked host list as well as the existing denylist.
- Created plugin to filter Moodle content from the database before it is sent to the client.
  This arose due to an issue where a supplier lost control of a domain and the domain was taken over by a malicious actor.
  As a result the embedded links to this now malicous domain were presented to all users.

Changed
-------

- Moved denylist to be a setting.

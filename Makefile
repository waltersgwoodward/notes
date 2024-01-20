# https://opensource.com/article/18/8/what-how-makefile
daily:
	php make/daily/run.php

yesterday:
	php make/yesterday/run.php

archive:
	php make/archive/run.php

reset:
	php make/reset/run.php

# Example: make ticket=EF-100 review => creates a new folder `EF-100` in `reviews/` with a new `notes.md` file
review:
	php make/review/run.php $(ticket)

# Example: make days=2 totalhours => returns total hours from last two archived timesheets
totalhours:
	php make/total-hours/run.php $(days)

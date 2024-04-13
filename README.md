# notes

A simple string templating tool I built using PHP and markdown for tracking my hours and progress on various projects.

| Command           | Description                                                             |
| ----------------- | ----------------------------------------------------------------------- |
| `make daily`      | Calculates and saves today's hours to `today.md`                        |
| `make yesterday`  | Similar to `make daily` except yesterday's date is used                 |
| `make archive`    | Archives the current timesheet in `today.md` and creates a new one      |
| `make reset`      | Creates a new timesheet without archiving the `today.md`                |
| `make totalhours` | Returns the total hours for a week (last five days/archived timesheets) |

# Ruby 3.3 Rack Starter (`ruby-rack`) — `carlossilveira.dev/lab/__LAB_SLUG__/`

Dual-mode Ruby 3.3 starter (`config.ru` for Phusion Passenger Rack + `index.cgi` for instant CloudLinux CGI execution) on Spaceship (`server58.shared.spaceship.host`).

## Runtime Details

- **CloudLinux Ruby 3.3**: `/opt/alt/ruby33/bin/ruby`
- **Local Development**:
  ```bash
  bundle install
  bundle exec rackup config.ru -p 9292
  ```

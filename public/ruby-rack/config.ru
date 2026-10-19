# frozen_string_literal: true

# Phusion Passenger Rack Entrypoint for https://carlossilveira.dev/lab/ruby-rack/
# Binary on Spaceship server58: /opt/alt/ruby33/bin/ruby

require 'json'
require 'time'
require 'digest'
require 'rbconfig'

app = lambda do |env|
  t0 = Process.clock_gettime(Process::CLOCK_MONOTONIC)
  rounds = 25_000
  digest = 'carlossilveira-ruby-33'
  rounds.times { |i| digest = Digest::SHA256.hexdigest("#{digest}#{i}") }
  total_ms = ((Process.clock_gettime(Process::CLOCK_MONOTONIC) - t0) * 1000.0).round(3)

  payload = {
    ok: true,
    app: 'ruby-rack',
    mode: 'passenger-rack',
    ruby_version: RUBY_VERSION,
    binary_path: RbConfig.ruby,
    gc_live_slots: GC.stat[:heap_live_slots],
    total_request_ms: total_ms,
    timestamp: Time.now.utc.iso8601
  }

  body = JSON.pretty_generate(payload)
  [
    200,
    {
      'content-type' => 'application/json; charset=utf-8',
      'access-control-allow-origin' => '*',
      'content-length' => body.bytesize.to_s
    },
    [body]
  ]
end

run app

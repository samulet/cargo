"use strict";

 angular.module("env.config", [])

.constant("REST_CONFIG", {
  "PROTOCOL": "http",
  "HOST": "api.cargo",
  "HOST_CONTEXT": "",
  "PORT": "8000",
  "DOMAIN": "cargo.dev",
  "BASE_URL": "http://api.cargo.dev:8000"
})

;
#!/usr/bin/env php
chmod 660 config/certificates/private.key
chmod 660 config/certificates/public.key
chown www-data config/certificates/private.key
chown www-data config/certificates/public.key

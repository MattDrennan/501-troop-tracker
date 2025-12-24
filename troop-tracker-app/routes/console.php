<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:calculate-trooper-achievements')
    ->daily()
    ->at('05:00');

<?php

use App\Jobs\PublishScheduledPosts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Schedule::job(new PublishScheduledPosts)->daily();

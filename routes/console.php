<?php

use App\Jobs\PublishScheduledPosts;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PublishScheduledPosts)->daily();

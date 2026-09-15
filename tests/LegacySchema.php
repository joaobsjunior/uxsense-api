<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates, in the in-memory SQLite test database, the subset of the legacy
 * MySQL schema used by the API (the production schema is managed outside of
 * this repository).
 */
trait LegacySchema
{
    protected function createLegacySchema(): void
    {
        Schema::create('administrator', function (Blueprint $table) {
            $table->increments('idadministrator');
            $table->string('name')->nullable();
            $table->string('login')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('token', 255)->nullable();
        });

        Schema::create('administrator_group', function (Blueprint $table) {
            $table->integer('idadministrator');
            $table->integer('idgroup');
        });

        Schema::create('manager_password', function (Blueprint $table) {
            $table->string('token', 255);
            $table->integer('idadministrator');
            $table->timestamp('date')->nullable()->useCurrent();
        });

        Schema::create('client', function (Blueprint $table) {
            $table->increments('idclient');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('register')->nullable();
            $table->string('password', 255)->nullable();
            $table->string('passwordrecovery', 255)->nullable();
            $table->string('sex', 1)->nullable();
            $table->date('datebirth')->nullable();
        });

        Schema::create('device', function (Blueprint $table) {
            $table->increments('iddevice');
            $table->integer('idclient')->nullable();
            $table->string('registratorid')->nullable();
            $table->string('platform')->nullable();
            $table->string('uuid')->nullable();
            $table->string('token', 255)->nullable();
        });

        Schema::create('group', function (Blueprint $table) {
            $table->increments('idgroup');
            $table->string('name')->nullable();
        });

        Schema::create('subgroup', function (Blueprint $table) {
            $table->increments('idsubgroup');
            $table->string('name')->nullable();
            $table->string('complement')->nullable();
            $table->integer('idgroup')->nullable();
        });

        Schema::create('unit', function (Blueprint $table) {
            $table->increments('idunit');
            $table->string('name')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
        });

        Schema::create('team', function (Blueprint $table) {
            $table->increments('idteam');
            $table->string('name')->nullable();
            $table->integer('idunit')->nullable();
            $table->integer('idsubgroup')->nullable();
        });

        Schema::create('team_client', function (Blueprint $table) {
            $table->integer('idteam');
            $table->integer('idclient');
        });

        Schema::create('question', function (Blueprint $table) {
            $table->increments('idquestion');
            $table->string('question')->nullable();
        });

        Schema::create('capture_technique', function (Blueprint $table) {
            $table->increments('idcapture_technique');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
        });

        Schema::create('scheduler', function (Blueprint $table) {
            $table->increments('idscheduler');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->integer('idquestion')->nullable();
            $table->integer('idteam')->nullable();
            $table->integer('capture_technique_id')->nullable();
            $table->integer('sent')->default(0);
        });

        Schema::create('answer', function (Blueprint $table) {
            $table->increments('idanswer');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->text('answer')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('idclient')->nullable();
            $table->integer('idscheduler')->nullable();
            $table->integer('capture_technique_id')->nullable();
        });
    }
}

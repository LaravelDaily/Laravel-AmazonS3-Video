<?php

$factory->define(App\Video::class, function (Faker\Generator $faker) {
    return [
        "name" => $faker->name,
    ];
});

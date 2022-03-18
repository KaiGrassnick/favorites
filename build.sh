#!/bin/bash

zip favorites/favorites.zip -x "favorites/build.sh" -x "favorites/bitbucket-pipelines.yml" -x "favorites/Dockerfile" -x "favorites/.git/*" -x "*.gitignore" -x "favorites/simplysamwp.zip" -x "favorites/info.json" -x "favorites/node_modules/*" -x "favorites/package-lock.json" -r favorites

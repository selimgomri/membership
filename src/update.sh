#!/bin/bash

echo "The web demon must have read/write access to the install directory"

echo "Pulling updates from GitHub repository"
git checkout master
git fetch --all
git reset --hard origin/master
echo "Update complete"
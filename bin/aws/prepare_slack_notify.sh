#!/bin/bash

BRANCH=$(git rev-parse --abbrev-ref HEAD | sed "s/\//-/")
sed "s/<CURRENT_GIT_BRANCH>/$BRANCH/" < bin/aws/slack_notify.sh.TEMPLATE > bin/aws/slack_notify.sh
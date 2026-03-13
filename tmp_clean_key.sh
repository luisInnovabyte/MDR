#!/bin/bash
cd /w/MDR
export FILTER_BRANCH_SQUELCH_WARNING=1
git filter-branch --force --tree-filter \
  'find HTML/chat/api -name "*.php" -exec sed -i "s/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/REMOVED_API_KEY/g" {} \; 2>/dev/null; true' \
  -- --all

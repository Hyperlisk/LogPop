
rm config.ini
rm core/access.ser
rm -r cache
rm plugins/seen_log.ser
git add *
git rm commit.sh
git commit -a
git push


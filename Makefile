
#
# Makefile fuer wp-championship
#

pot:
	@xgettext -L PHP -k --keyword=_e  --keyword=__ --from-code=utf-8 --default-domain=wpcs --output=wp-championship.pot *.php;


all: upload

upload:
	rm -f *~
	ncftpput -R -u w008c3be -p tw3nlanc  ftp.tuxlog.de em2008/wp-content/plugins/wp-championship/ *

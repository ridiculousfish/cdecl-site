LAMBDA_FILES := exe/cdecl_linux run_cdecl.py

cdecl_lambda.zip:
	rm -f $@
	cd cdecl && zip ../$@ ${LAMBDA_FILES}

clean:
	rm -f cdecl_lambda.zip

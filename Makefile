cdecl.zip:
	rm -f $@
	cd cdecl && zip ../$@ *

clean:
	rm -f cdecl.zip

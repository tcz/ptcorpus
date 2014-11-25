for i in 0 1 2 3 4 5 6 7 8 9 a b c d e f; do
	for j in 0 1 2 3 4 5 6 7 8 9 a b c d e f; do
		mkdir -p "/tmp/fbcache/$i/$j"
	done
done

for i in /tmp/freebasecache/*; do
	FILE=$(basename "$i")
	LTR1="${FILE:0:1}"
	LTR2="${FILE:1:1}"
	REST="${FILE:2:-1}"
	cp "$i" "/tmp/fbcache/$LTR1/$LTR2/$REST"
	echo $i
done
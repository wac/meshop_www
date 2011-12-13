import sys

# Given a hash
# Find the best matches - output the number of bits that match

def usage():
    print sys.argv[0], "[name to lookup]", "[list of files to parse]"
    exit(1)

bctable={}
bitmask16=int("1"*16,2)

def bitcount16(v):
    if v in bctable:
        return bctable[v]

    c = 0
    while v:
        v &= v -1;
        c += 1;

    bctable[v]=c
    return c

def bitcount(v):
    c=0
    while v:
        c += bitcount16(v & bitmask16)
        v = v >> 16
    return c

sep='|'

if (len(sys.argv) <= 1):
    usage()

sys.argv.pop(0)

countmode='match'
if sys.argv[0]=="--count1":
    countmode='count1'
    print "# Count1 mode"
    sys.argv.pop(0)


#testhash=sys.argv.pop(0)
teststr=sys.argv.pop(0)
testname=''
totalhash = {}
bitmask = int("1"*1000, 2)

for f in sys.argv:
    h={}
    testhash=0

    for line in open(f):
        tuple=line.strip().split(sep)
        if line[0]=='#':
            print line
            continue
        key=tuple[0]
        val=int(tuple[2])
        h[key]=int(val)
        if key.startswith(teststr):
            testhash=val
            testname=key

    if countmode=='match':
        testhash=~testhash

    print "#", testname, "Testhash:", testhash

    for key,val in h.iteritems():
        if countmode=='count1':
            count=bitcount(val & testhash)
        else:
            count=bitcount((val ^ testhash) & bitmask)
        if key in totalhash:
            totalhash[key] += count
        else:
            totalhash[key] = count

for key,val in totalhash.iteritems():
    print key+sep+str(val)


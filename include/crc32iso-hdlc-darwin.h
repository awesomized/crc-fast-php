#define FFI_SCOPE "CRC32ISOHDLC"
#define FFI_LIB "build/crc32fast-lib-rust/target/release/libcrc32fast_lib.dylib"

typedef struct HasherHandle HasherHandle;

HasherHandle *hasher_new();

void hasher_write(HasherHandle *handle, const char *data, uintptr_t len);

uint32_t hasher_finalize(HasherHandle *handle);

uint32_t crc32_hash(const char *data, uintptr_t len);

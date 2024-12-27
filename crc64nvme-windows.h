#define FFI_SCOPE "CRC64NVME"
#define FFI_LIB "build/target/release/libcrc64fast_nvme.dll"

typedef struct DigestHandle DigestHandle;

DigestHandle* digest_new(void);

void digest_write(DigestHandle* handle, const char* data, size_t len);

uint64_t digest_sum64(const DigestHandle* handle);

void digest_free(DigestHandle* handle);

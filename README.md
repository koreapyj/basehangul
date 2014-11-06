# BaseHangul

BaseHangul은 한글을 사용한 바이너리 인코더입니다.

- 한글 자모를 조합하여 나타냅니다. KS C 5601에서 정한 자모 조합 중 사전순으로 앞에서부터 1024개의 조합만을 사용하여 10비트가 한 글자로 표현됩니다.
- 작동 시험은 http://api.dcmys.jp/%EB%B7%81%EC%96%B4%EB%B2%88%EC%97%AD%EA%B8%B0/ 에서 해볼 수 있습니다.
- 자세한 설명은 https://basehangul.github.io 를 참고하세요.
- 이 코드는 어디까지나 예제로, 스펙과 다르게 작동하는 부분이 있다면 버그입니다.

## 예제
```php
$basehan = new BaseHangul("UTF-8");     // 인수로 출력되는 한글의 문자셋을 지정합니다. 지정하지 않으면 UTF-8로 처리됩니다.
                                        // 이 인수는 입력 내용과는 무관합니다.
$str = 'This is an encoded string';
$encoded  = $basehan->encode($str);     // 데이터를 BaseHangul로 인코딩합니다. 이 함수는 binary-safe합니다.
$plaintext= $basehan->decode($encoded); // BaseHangul을 바이너리 데이터로 복원합니다.

echo $encoded."\n";
echo $plaintext."\n";
```
실행하면 아래와 같이 출력됩니다.
```
넥라똔먈늴멥갯놓궂뗐밸뮤뉴뗐뀄굡덜멂똑뚤
This is an encoded string
```

## 라이센스
Public Domain

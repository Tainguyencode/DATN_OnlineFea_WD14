<?php

namespace Database\Seeders\Demo;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use Illuminate\Support\Str;

class DemoCourseSeeder
{
    private array $courseTemplates = [
        // 1. Phát triển Web (Parent: 1, Sub: 2)
        [
            'category_sub_name' => 'Phát triển Web',
            'courses' => [
                [
                    'title' => 'Khóa học Laravel 11 & Vue.js Fullstack E-Commerce Toàn Diện',
                    'slug' => 'demo-laravel-11-vuejs-fullstack-ecommerce',
                    'description' => 'Xây dựng website bán hàng hiện đại chuẩn Micro-Frontend với Laravel 11 API, Inertia.js Vue 3, Tailwind CSS, thanh toán trực tuyến PayOS và SePay.',
                    'level' => 'intermediate',
                    'price' => 1290000,
                    'sale_price' => 890000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600',
                    'tags' => ['laravel', 'vuejs', 'ecommerce', 'fullstack', 'api'],
                    'objectives' => ['Xây dựng RESTful API chuyên nghiệp với Laravel 11', 'Tích hợp cổng thanh toán PayOS và SePay tự động', 'Quản lý state với Pinia và Inertia.js'],
                    'requirements' => ['Đã biết lập trình PHP cơ bản', 'Có kiến thức cơ bản về HTML/CSS/JS'],
                    'target_audience' => ['Lập trình viên muốn trở thành Fullstack Web Developer', 'Sinh viên ngành CNTT'],
                ],
                [
                    'title' => 'Lập trình Web Frontend Hiện Đại với React 18, Next.js 14 & TypeScript',
                    'slug' => 'demo-react-18-nextjs-typescript-masterclass',
                    'description' => 'Làm chủ Server Components, App Router, Server Actions, Tailwind CSS và tối ưu hóa SEO vượt trội cho ứng dụng Web chuẩn Enterprise.',
                    'level' => 'advanced',
                    'price' => 1490000,
                    'sale_price' => 990000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=600',
                    'tags' => ['react', 'nextjs', 'typescript', 'frontend', 'seo'],
                    'objectives' => ['Thành thạo React Server Components và App Router', 'Xây dựng UI responsive đẳng cấp với Tailwind CSS', 'Deploy ứng dụng lên Vercel và AWS'],
                    'requirements' => ['Hiểu rõ JavaScript ES6+', 'Biết cơ bản về Git'],
                    'target_audience' => ['Frontend Developer muốn nâng cấp lên Next.js 14'],
                ],
                [
                    'title' => 'Xây dựng Web Microservices với NestJS, Node.js & Docker',
                    'slug' => 'demo-nestjs-microservices-nodejs-docker',
                    'description' => 'Kiến trúc hệ thống Backend phân tán tải cao với NestJS, gRPC, RabbitMQ Message Queue, Redis In-Memory Cache và Docker Swarm.',
                    'level' => 'advanced',
                    'price' => 1690000,
                    'sale_price' => 1190000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600',
                    'tags' => ['nestjs', 'nodejs', 'microservices', 'docker', 'rabbitmq'],
                    'objectives' => ['Thiết kế kiến trúc hướng sự kiện Event-Driven Architecture', 'Triển khai RabbitMQ và Redis Cluster', 'Container hóa dịch vụ với Docker Compose'],
                    'requirements' => ['Nắm vững JavaScript/TypeScript', 'Có kinh nghiệm với Node.js Backend'],
                    'target_audience' => ['Backend Developer muốn lên Senior/Architect'],
                ],
            ]
        ],
        // 2. Phát triển ứng dụng Mobile (Parent: 1, Sub: 3)
        [
            'category_sub_name' => 'Phát triển ứng dụng Mobile',
            'courses' => [
                [
                    'title' => 'Lập trình Mobile Đa Nền Tảng với Flutter 3 & BLoC Pattern Thực Chiến',
                    'slug' => 'demo-flutter-3-bloc-pattern-mastery',
                    'description' => 'Xây dựng ứng dụng đặt đồ ăn trực tuyến (Food Delivery) trên iOS và Android từ A-Z với Flutter, Dart, Clean Architecture và Firebase Cloud Messaging.',
                    'level' => 'intermediate',
                    'price' => 1190000,
                    'sale_price' => 790000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600',
                    'tags' => ['flutter', 'dart', 'mobile', 'ios', 'android', 'bloc'],
                    'objectives' => ['Làm chủ BLoC Pattern và Clean Architecture', 'Xử lý Real-time Notifications với Firebase', 'Tối ưu hiệu năng 60 FPS mượt mà'],
                    'requirements' => ['Biết lập trình hướng đối tượng cơ bản'],
                    'target_audience' => ['Người muốn làm ứng dụng di động cho cả iOS và Android'],
                ],
                [
                    'title' => 'Phát triển Ứng dụng iOS Native Chuyên Sâu với Swift 5 & SwiftUI',
                    'slug' => 'demo-ios-native-swift-swiftui-pro',
                    'description' => 'Học phát triển ứng dụng iPhone, iPad với SwiftUI, Combine Framework, CoreData, StoreKit In-App Purchase và đưa app lên App Store.',
                    'level' => 'advanced',
                    'price' => 1590000,
                    'sale_price' => 1090000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1526470608268-f674ce90ebd4?w=600',
                    'tags' => ['ios', 'swift', 'swiftui', 'apple', 'mobile'],
                    'objectives' => ['Thiết kế UI Declarative với SwiftUI', 'Quản lý dữ liệu offline với CoreData/SwiftData', 'Quy trình Review và Publish ứng dụng lên App Store'],
                    'requirements' => ['Sử dụng máy tính macOS hoặc Xcode Cloud'],
                    'target_audience' => ['Mobile Developer muốn chuyên môn hóa nền tảng iOS'],
                ],
            ]
        ],
        // 3. Khoa học dữ liệu (Parent: 1, Sub: 5)
        [
            'category_sub_name' => 'Khoa học dữ liệu',
            'courses' => [
                [
                    'title' => 'Python for Data Science, Pandas & Phân Tích Dữ Liệu Kinh Doanh',
                    'slug' => 'demo-python-data-science-pandas-analytics',
                    'description' => 'Khám phá và phân tích tập dữ liệu lớn với NumPy, Pandas, Matplotlib, Seaborn, xây dựng biểu đồ Dashboard tương tác trực quan.',
                    'level' => 'beginner',
                    'price' => 890000,
                    'sale_price' => 590000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600',
                    'tags' => ['python', 'datascience', 'pandas', 'analytics', 'data'],
                    'objectives' => ['Xử lý làm sạch và biến đổi dữ liệu với Pandas', 'Trực quan hóa Insight qua biểu đồ chuyên nghiệp', 'Áp dụng vào phân tích hành vi khách hàng'],
                    'requirements' => ['Không yêu cầu kiến thức lập trình từ trước'],
                    'target_audience' => ['Data Analyst, Marketer, Sinh viên Kinh tế/Kỹ thuật'],
                ],
                [
                    'title' => 'Kỹ Thuật Xử Lý Dữ Liệu Lớn với Apache Spark, PySpark & Kafka',
                    'slug' => 'demo-bigdata-apache-spark-pyspark-kafka',
                    'description' => 'Xây dựng Data Pipeline thời gian thực, xử lý Streaming Data quy mô Terabytes trên kiến trúc Delta Lake và Databricks.',
                    'level' => 'advanced',
                    'price' => 1790000,
                    'sale_price' => 1290000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=600',
                    'tags' => ['bigdata', 'spark', 'kafka', 'dataengineering', 'pyspark'],
                    'objectives' => ['Thiết kế hệ thống Data Lakehouse', 'Xử lý luồng sự kiện Real-time với Kafka và Spark Streaming', 'Tối ưu hóa Shuffle và Partitioning'],
                    'requirements' => ['Biết lập trình Python và kiến thức cơ bản về SQL'],
                    'target_audience' => ['Data Engineer, Backend Engineer muốn chuyển sang Big Data'],
                ],
            ]
        ],
        // 4. Trí tuệ nhân tạo & Machine Learning (Parent: 1, Sub: 6)
        [
            'category_sub_name' => 'Trí tuệ nhân tạo và Machine Learning',
            'courses' => [
                [
                    'title' => 'Machine Learning & Deep Learning Thực Chiến với PyTorch & YOLOv8',
                    'slug' => 'demo-machine-learning-pytorch-yolov8',
                    'description' => 'Huấn luyện mô hình Thị giác máy tính (Computer Vision), nhận diện vật thể thời gian thực, phân đoạn ảnh và Fine-tuning mô hình AI.',
                    'level' => 'intermediate',
                    'price' => 1490000,
                    'sale_price' => 990000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600',
                    'tags' => ['ai', 'machinelearning', 'deeplearning', 'pytorch', 'computervision'],
                    'objectives' => ['Hiểu sâu cơ chế Mạng Neural tích chập (CNN)', 'Train mô hình Object Detection với YOLOv8', 'Deploy AI Model lên Web API với FastAPI'],
                    'requirements' => ['Biết ngôn ngữ Python và Đại số tuyến tính cơ bản'],
                    'target_audience' => ['Kỹ sư AI/ML, Nghiên cứu sinh'],
                ],
                [
                    'title' => 'Lập Trình Ứng Dụng GenAI, RAG & LLM với LangChain & OpenAI API',
                    'slug' => 'demo-genai-rag-llm-langchain-openai',
                    'description' => 'Xây dựng trợ lý ảo thông minh (AI Chatbot), hệ thống hỏi đáp tài liệu doanh nghiệp RAG với Vector Database Milvus/Pinecone và LangChain.',
                    'level' => 'intermediate',
                    'price' => 1390000,
                    'sale_price' => 950000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=600',
                    'tags' => ['genai', 'llm', 'rag', 'langchain', 'openai'],
                    'objectives' => ['Xây dựng ứng dụng hỏi đáp văn bản thông minh (RAG Pipeline)', 'Tích hợp Vector Database và Embeddings', 'Prompt Engineering chuẩn công nghiệp'],
                    'requirements' => ['Có kinh nghiệm lập trình Python hoặc JavaScript'],
                    'target_audience' => ['Software Engineer muốn tích hợp AI vào sản phẩm'],
                ],
            ]
        ],
        // 5. Ngôn ngữ lập trình (Parent: 1, Sub: 7)
        [
            'category_sub_name' => 'Ngôn ngữ lập trình',
            'courses' => [
                [
                    'title' => 'Lập Trình Golang Nền Tảng & Xây Dựng High-Performance Microservices',
                    'slug' => 'demo-golang-high-performance-microservices',
                    'description' => 'Làm chủ Concurrency với Goroutines & Channels, xây dựng Web Service siêu nhẹ với Gin Gonic, tối ưu bộ nhớ và benchmark.',
                    'level' => 'intermediate',
                    'price' => 1190000,
                    'sale_price' => 790000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=600',
                    'tags' => ['golang', 'go', 'concurrency', 'microservices', 'backend'],
                    'objectives' => ['Làm chủ mô hình Concurrency đa luồng trong Go', 'Viết RESTful API hiệu năng cao với Gin Framework', 'Testing và Profiling tối ưu hóa bộ nhớ'],
                    'requirements' => ['Đã biết một ngôn ngữ lập trình bất kỳ (C++, Java, JS...)'],
                    'target_audience' => ['Backend Developer muốn tăng tốc hiệu năng hệ thống'],
                ],
                [
                    'title' => 'Java Core Nâng Cao & Lập Trình Spring Boot 3 Chuẩn Doanh Nghiệp',
                    'slug' => 'demo-java-core-spring-boot-3-enterprise',
                    'description' => 'Học Java 21 LTS, Virtual Threads, Spring Boot 3, Spring Data JPA, Spring Security JWT và triển khai dự án ngân hàng mẫu.',
                    'level' => 'advanced',
                    'price' => 1390000,
                    'sale_price' => 990000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1537432376769-00f5c2f4c8d2?w=600',
                    'tags' => ['java', 'springboot', 'spring', 'enterprise', 'backend'],
                    'objectives' => ['Nắm vững Virtual Threads và tính năng mới trong Java 21', 'Bảo mật API với Spring Security 6 & OAuth2', 'Thiết kế cơ sở dữ liệu với Spring Data JPA & Hibernate'],
                    'requirements' => ['Hiểu kiến thức OOP cơ bản'],
                    'target_audience' => ['Java Developer muốn làm việc tại các dự án ngân hàng/tài chính'],
                ],
            ]
        ],
        // 6. Cơ sở dữ liệu (Parent: 1, Sub: 8)
        [
            'category_sub_name' => 'Cơ sở dữ liệu',
            'courses' => [
                [
                    'title' => 'Tối Ưu Hóa & Quản Trị MySQL & PostgreSQL Cho Hệ Thống Lớn',
                    'slug' => 'demo-database-optimization-mysql-postgresql',
                    'description' => 'Học kỹ thuật đánh Index (B-Tree, Hash, GIN), phân tích EXPLAIN Query Execution Plan, tối ưu hóa Slow Query, Partitioning và Master-Slave Replication.',
                    'level' => 'advanced',
                    'price' => 1290000,
                    'sale_price' => 850000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=600',
                    'tags' => ['database', 'mysql', 'postgresql', 'optimization', 'sql'],
                    'objectives' => ['Đọc hiểu và tối ưu Execution Plan trong MySQL & Postgres', 'Thiết kế chiến lược Indexing chính xác giảm tải CPU', 'Thiết lập Replication và Failover tự động'],
                    'requirements' => ['Biết viết câu lệnh SQL SELECT/INSERT/UPDATE/JOIN'],
                    'target_audience' => ['Database Administrator (DBA), Backend Developer'],
                ],
                [
                    'title' => 'Thiết Kế CSDL NoSQL với MongoDB & Redis In-Memory Cache',
                    'slug' => 'demo-nosql-mongodb-redis-caching',
                    'description' => 'Mô hình hóa dữ liệu Document-based, Sharding, Replica Set với MongoDB và chiến lược Caching (Write-Through, Cache-Aside) với Redis.',
                    'level' => 'intermediate',
                    'price' => 990000,
                    'sale_price' => 690000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=600',
                    'tags' => ['nosql', 'mongodb', 'redis', 'caching', 'database'],
                    'objectives' => ['Mô hình hóa dữ liệu linh hoạt trên MongoDB', 'Ứng dụng Redis làm Cache, Queue và Pub/Sub', 'Xử lý bài toán Cache Stampede và Cache Avalanche'],
                    'requirements' => ['Có hiểu biết cơ bản về cơ sở dữ liệu quan hệ'],
                    'target_audience' => ['Backend Developer, DevOps Engineer'],
                ],
            ]
        ],
        // 7. Kiểm thử phần mềm (Parent: 1, Sub: 9)
        [
            'category_sub_name' => 'Kiểm thử phần mềm',
            'courses' => [
                [
                    'title' => 'Kiểm Thử Tự Động Toàn Diện với Playwright & TypeScript',
                    'slug' => 'demo-automation-testing-playwright-typescript',
                    'description' => 'Học viết End-to-End Test (E2E), API Test, Visual Regression Testing với Playwright, Page Object Model (POM) và tích hợp CI/CD.',
                    'level' => 'intermediate',
                    'price' => 1090000,
                    'sale_price' => 750000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600',
                    'tags' => ['testing', 'automation', 'playwright', 'typescript', 'qa'],
                    'objectives' => ['Thiết kế Automation Test Framework theo chuẩn POM', 'Tự động hóa kiểm thử API và UI trên đa trình duyệt', 'Tích hợp chạy test tự động trên GitHub Actions'],
                    'requirements' => ['Biết cơ bản về JavaScript hoặc TypeScript'],
                    'target_audience' => ['Manual QA muốn chuyển sang Automation QA, Developers'],
                ],
                [
                    'title' => 'Khóa Học Kiểm Thử Phần Mềm Thủ Công (Manual Testing) Chuẩn Quốc Tế',
                    'slug' => 'demo-manual-testing-istqb-foundation',
                    'description' => 'Luyện thi chứng chỉ ISTQB Foundation, kỹ thuật thiết kế Test Case, quản lý lỗi (Bug Lifecycle) với Jira và viết báo cáo kiểm thử chuyên nghiệp.',
                    'level' => 'beginner',
                    'price' => 790000,
                    'sale_price' => 490000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600',
                    'tags' => ['qa', 'qc', 'manualtesting', 'istqb', 'jira'],
                    'objectives' => ['Nắm vững quy trình phát triển và kiểm thử phần mềm (STLC)', 'Viết Test Case chi tiết bao phủ mọi kịch bản nghiệp vụ', 'Sử dụng Jira để log bug và tracking tiến độ'],
                    'requirements' => ['Dành cho người mới bắt đầu không cần biết code'],
                    'target_audience' => ['Sinh viên mới ra trường, người chuyển ngành sang nghề Tester'],
                ],
            ]
        ],
        // 8. An ninh mạng & Bảo mật (Parent: 31, Sub: 33)
        [
            'category_sub_name' => 'An ninh mạng',
            'courses' => [
                [
                    'title' => 'Bảo Mật Ứng Dụng Web & Kỹ Thuật Tấn Công Thử Nghiệm OWASP Top 10',
                    'slug' => 'demo-web-security-owasp-top-10-pentesting',
                    'description' => 'Phát hiện và phòng chống các lỗ hổng bảo mật nghiêm trọng: SQL Injection, XSS, CSRF, SSRF, Broken Access Control với Burp Suite và Kali Linux.',
                    'level' => 'advanced',
                    'price' => 1590000,
                    'sale_price' => 1090000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600',
                    'tags' => ['security', 'cybersecurity', 'owasp', 'pentest', 'ethicalhacking'],
                    'objectives' => ['Thực hành khai thác và vá lỗi OWASP Top 10', 'Sử dụng Burp Suite chuyên nghiệp để đánh giá an ninh web', 'Thiết lập cơ chế phòng thủ WAF và CSP'],
                    'requirements' => ['Hiểu về HTTP/HTTPS, Web Architecture và Networking'],
                    'target_audience' => ['Security Engineer, Ethical Hacker, Web Developer'],
                ],
                [
                    'title' => 'Quản Trị Mạng & Giám Sát An Ninh SOC Blue Team Chuyên Nghiệp',
                    'slug' => 'demo-soc-blue-team-security-operations',
                    'description' => 'Phân tích mã độc, giám sát lưu lượng mạng với Wireshark, cấu hình SIEM với Splunk & Wazuh, ứng phó sự cố an ninh thông tin (Incident Response).',
                    'level' => 'intermediate',
                    'price' => 1390000,
                    'sale_price' => 950000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1563986768494-4dee2763ff3f?w=600',
                    'tags' => ['soc', 'blueteam', 'siem', 'security', 'network'],
                    'objectives' => ['Phân tích Log và cảnh báo an ninh thời gian thực trên SIEM', 'Kỹ thuật điều tra dấu vết số (Digital Forensics)', 'Xây dựng kịch bản ứng phó sự cố tấn công mạng'],
                    'requirements' => ['Có hiểu biết về hệ điều hành Linux và mạng máy tính'],
                    'target_audience' => ['Chuyên viên SOC, Quản trị mạng hệ thống'],
                ],
            ]
        ],
        // 9. Điện toán đám mây & DevOps (Parent: 31, Sub: 36 & 38)
        [
            'category_sub_name' => 'Điện toán đám mây',
            'courses' => [
                [
                    'title' => 'Chinh Phục Chứng Chỉ AWS Certified Solutions Architect Associate',
                    'slug' => 'demo-aws-solutions-architect-associate',
                    'description' => 'Làm chủ các dịch vụ cốt lõi của AWS: EC2, S3, RDS, VPC, Lambda Serverless, Auto Scaling, IAM và thiết kế kiến trúc High Availability.',
                    'level' => 'intermediate',
                    'price' => 1490000,
                    'sale_price' => 990000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600',
                    'tags' => ['aws', 'cloud', 'solutionsarchitect', 'serverless', 'devops'],
                    'objectives' => ['Thiết kế kiến trúc hệ thống chịu tải cao trên AWS', 'Tối ưu hóa chi phí và bảo mật dữ liệu đám mây', 'Tự tin vượt qua kỳ thi AWS SAA-C03'],
                    'requirements' => ['Hiểu biết cơ bản về mô hình Client-Server và mạng máy tính'],
                    'target_audience' => ['Kỹ sư Cloud, DevOps, System Administrator'],
                ],
                [
                    'title' => 'Docker & Kubernetes: Triển Khai & Vận Hành Hệ Thống Tải Cao',
                    'slug' => 'demo-docker-kubernetes-production-mastery',
                    'description' => 'Container hóa ứng dụng với Docker, quản lý cụm K8s với Helm, Ingress Controller, Persistent Volume, HPA Autoscaling và GitOps với ArgoCD.',
                    'level' => 'advanced',
                    'price' => 1690000,
                    'sale_price' => 1190000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1667372393119-3d4c48d07fc9?w=600',
                    'tags' => ['docker', 'kubernetes', 'k8s', 'devops', 'helm', 'argocd'],
                    'objectives' => ['Xây dựng cụm Kubernetes Cluster chuẩn Production', 'Quản lý cấu hình và deployment với Helm Chart', 'Thiết lập quy trình GitOps tự động hóa với ArgoCD'],
                    'requirements' => ['Sử dụng thành thạo Linux dòng lệnh'],
                    'target_audience' => ['DevOps Engineer, SRE, Tech Lead'],
                ],
            ]
        ],
        // 10. Thiết kế UI/UX (Parent: 58, Sub: 60)
        [
            'category_sub_name' => 'Thiết kế UI/UX',
            'courses' => [
                [
                    'title' => 'Figma UI/UX Masterclass: Từ Ý Tưởng Đến Thiết Kế Sản Phẩm Hoàn Chỉnh',
                    'slug' => 'demo-figma-ui-ux-design-masterclass',
                    'description' => 'Làm chủ Auto Layout, Component Variant, Interactive Prototype, Design System đồng bộ hóa Developer Handoff trên Figma.',
                    'level' => 'beginner',
                    'price' => 990000,
                    'sale_price' => 690000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1581291518655-9523c932ded8?w=600',
                    'tags' => ['figma', 'uiux', 'design', 'productdesign', 'prototype'],
                    'objectives' => ['Xây dựng Design System quy mô lớn với Figma Token', 'Tạo Prototype tương tác chân thực cho mobile & web', 'Quy trình bàn giao thiết kế chuẩn cho lập trình viên'],
                    'requirements' => ['Máy tính cài đặt trình duyệt web hoặc Figma Desktop'],
                    'target_audience' => ['UI/UX Designer, Frontend Developer, Product Owner'],
                ],
                [
                    'title' => 'Nghiên Cứu Trải Nghiệm Người Dùng (UX Research) & Usability Testing',
                    'slug' => 'demo-ux-research-usability-testing',
                    'description' => 'Phương pháp phỏng vấn người dùng (User Interview), xây dựng Persona, Customer Journey Map, Wireframing và đo lường chỉ số UX (SUS, NPS).',
                    'level' => 'intermediate',
                    'price' => 890000,
                    'sale_price' => 590000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=600',
                    'tags' => ['ux', 'research', 'usability', 'uxdesign', 'product'],
                    'objectives' => ['Lập kế hoạch và triển khai nghiên cứu người dùng định tính & định lượng', 'Vẽ Customer Journey Map và User Flow chuẩn xác', 'Tổ chức các buổi Usability Testing tìm ra điểm nghẽn'],
                    'requirements' => ['Tư duy hướng người dùng, không cần biết vẽ đẹp'],
                    'target_audience' => ['UX Designer, Product Manager, Business Analyst'],
                ],
            ]
        ],
        // 11. Tin học văn phòng & Power BI (Parent: 40, Sub: 41 & 46)
        [
            'category_sub_name' => 'Microsoft Excel',
            'courses' => [
                [
                    'title' => 'Microsoft Excel Từ Cơ Bản Đến Nâng Cao & Tự Động Hóa với VBA/Macro',
                    'slug' => 'demo-microsoft-excel-advanced-vba-macros',
                    'description' => 'Làm chủ các hàm tìm kiếm (XLOOKUP, INDEX-MATCH), PivotTable phân tích đa chiều, xây dựng Dashboard báo cáo tài chính và viết macro VBA tự động.',
                    'level' => 'beginner',
                    'price' => 590000,
                    'sale_price' => 390000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1543286386-713bdd548da4?w=600',
                    'tags' => ['excel', 'vba', 'office', 'data', 'finance'],
                    'objectives' => ['Xử lý số liệu nhanh chóng với hơn 50 hàm Excel thông dụng', 'Tạo Dashboard động chuyên nghiệp cho ban giám đốc', 'Tự động hóa các tác vụ lặp đi lặp lại bằng VBA Macro'],
                    'requirements' => ['Máy tính cài đặt Microsoft Excel'],
                    'target_audience' => ['Nhân viên văn phòng, kế toán, sinh viên mọi ngành'],
                ],
                [
                    'title' => 'Xây Dựng Báo Cáo Doanh Nghiệp Tương Tác với Microsoft Power BI',
                    'slug' => 'demo-microsoft-power-bi-business-dashboard',
                    'description' => 'Kết nối đa nguồn dữ liệu, biến đổi dữ liệu với Power Query, viết công thức DAX tính toán chỉ số KPI và thiết kế Interactive Dashboard trực quan.',
                    'level' => 'intermediate',
                    'price' => 890000,
                    'sale_price' => 590000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600',
                    'tags' => ['powerbi', 'bi', 'dax', 'dashboard', 'analytics'],
                    'objectives' => ['Mô hình hóa dữ liệu Star Schema trong Power BI', 'Viết hàm DAX nâng cao tính toán tăng trưởng và tỷ lệ', 'Xuất bản và chia sẻ báo cáo lên Power BI Service'],
                    'requirements' => ['Hiểu biết cơ bản về bảng tính Excel'],
                    'target_audience' => ['Data Analyst, Chuyên viên phân tích kinh doanh, Quản lý'],
                ],
            ]
        ],
        // 12. Digital Marketing (Parent: 68, Sub: 69 & 70)
        [
            'category_sub_name' => 'Digital Marketing',
            'courses' => [
                [
                    'title' => 'Digital Marketing Toàn Diện: SEO, Facebook Ads, Google Ads & TikTok Ads',
                    'slug' => 'demo-digital-marketing-omnichannel-growth',
                    'description' => 'Xây dựng chiến lược Marketing đa kênh, tối ưu chi phí quảng cáo (ROAS), viết Content chuyển đổi cao và đo lường chuyển đổi với Google Analytics 4 (GA4).',
                    'level' => 'beginner',
                    'price' => 990000,
                    'sale_price' => 690000,
                    'is_featured' => true,
                    'thumbnail' => 'https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=600',
                    'tags' => ['marketing', 'digitalmarketing', 'seo', 'ads', 'growth'],
                    'objectives' => ['Thiết lập và tối ưu chiến dịch Google Ads, Facebook Ads và TikTok Ads', 'Đọc hiểu báo cáo và phễu chuyển đổi trên GA4', 'Chiến lược Content Viral tăng độ phủ thương hiệu'],
                    'requirements' => ['Không yêu cầu kinh nghiệm trước'],
                    'target_audience' => ['Chủ doanh nghiệp nhỏ, Marketer, Freelancer'],
                ],
                [
                    'title' => 'Khóa Học SEO Web Masterclass: Đưa Từ Khóa Lên Top 1 Google Bền Vững',
                    'slug' => 'demo-seo-web-masterclass-top-google',
                    'description' => 'Nghiên cứu từ khóa chuyên sâu với Ahrefs/SEMrush, kỹ thuật On-page SEO, Technical SEO tối ưu Core Web Vitals, xây dựng Entity và Backlink an toàn.',
                    'level' => 'intermediate',
                    'price' => 790000,
                    'sale_price' => 490000,
                    'is_featured' => false,
                    'thumbnail' => 'https://images.unsplash.com/photo-1562577309-4932fdd64cd1?w=600',
                    'tags' => ['seo', 'google', 'traffic', 'content', 'marketing'],
                    'objectives' => ['Tìm kiếm từ khóa ngách ít cạnh tranh tỷ lệ chuyển đổi cao', 'Tối ưu tốc độ tải trang đạt điểm xanh PageSpeed Insights', 'Xây dựng cấu trúc Silo Page chuẩn SEO'],
                    'requirements' => ['Có website WordPress hoặc blog để thực hành'],
                    'target_audience' => ['SEOer, Blogger, Quản trị website'],
                ],
            ]
        ],
    ];

    public function run(array $instructors, ?callable $output = null): array
    {
        $log = $output ?: fn(string $msg) => null;
        $log('--- Bắt đầu nạp 60+ Khóa học Demo chuẩn kèm Chương, Bài học & Quizzes ---');

        $activeSubCategories = Category::whereNotNull('parent_id')
            ->where('status', 1)
            ->get()
            ->keyBy('name');

        $createdCourses = [];
        $instructorCount = count($instructors);
        $courseIdx = 0;

        // 1. Nạp từ các mẫu danh mục chuẩn
        foreach ($this->courseTemplates as $group) {
            $subCategory = $activeSubCategories->get($group['category_sub_name']);
            if (! $subCategory) {
                // Fallback tìm tương đối
                $subCategory = Category::whereNotNull('parent_id')
                    ->where('name', 'like', '%' . $group['category_sub_name'] . '%')
                    ->first();
            }

            $catId = $subCategory ? $subCategory->id : 2; // Fallback to subcategory ID 2

            foreach ($group['courses'] as $cData) {
                $instructor = $instructors[$courseIdx % $instructorCount];
                $course = $this->createSingleCourse($cData, $catId, $instructor->id);
                $createdCourses[] = $course;
                $courseIdx++;
            }
        }

        // 2. Tạo bổ sung để đảm bảo MỌI subcategory hoạt động đều có ít nhất 2 khóa học và tổng số đạt >= 65 khóa
        $allActiveSubs = Category::whereNotNull('parent_id')->where('status', 1)->get();
        foreach ($allActiveSubs as $subCat) {
            $existingCount = Course::where('category_id', $subCat->id)->count();
            $needed = max(0, 2 - $existingCount);

            for ($k = 1; $k <= $needed; $k++) {
                $instructor = $instructors[$courseIdx % $instructorCount];
                $title = "Khóa học Chuyên Sâu: {$subCat->name} Thực Chiến Tập {$k}";
                $slug = 'demo-' . Str::slug($subCat->name) . "-thuc-chien-tap-{$k}-" . ($courseIdx + 1);

                $cData = [
                    'title' => $title,
                    'slug' => $slug,
                    'description' => "Chương trình đào tạo thực tế bám sát dự án doanh nghiệp về {$subCat->name}. Cung cấp kiến thức từ cơ bản đến chuyên sâu, bài tập dự án thực chiến và cấp chứng chỉ sau hoàn thành.",
                    'level' => ($k === 1) ? 'beginner' : 'intermediate',
                    'price' => ($k === 1) ? 0 : (690000 + ($courseIdx % 6) * 150000),
                    'sale_price' => ($k === 1) ? 0 : (490000 + ($courseIdx % 6) * 100000),
                    'is_featured' => ($courseIdx % 5 === 0),
                    'thumbnail' => 'https://images.unsplash.com/photo-' . (1500000000000 + ($courseIdx * 789123) % 80000000) . '?w=600',
                    'tags' => [Str::slug($subCat->name), 'demo', 'thuc-chien', 'onlinefea'],
                    'objectives' => ["Nắm vững kiến thức cốt lõi về {$subCat->name}", 'Ứng dụng giải quyết bài toán thực tế', 'Tự tin ứng tuyển vị trí chuyên môn'],
                    'requirements' => ['Máy tính cá nhân có kết nối Internet', 'Tinh thần chủ động học tập'],
                    'target_audience' => ['Học viên muốn phát triển kỹ năng nghề nghiệp', 'Sinh viên các ngành liên quan'],
                ];

                $course = $this->createSingleCourse($cData, $subCat->id, $instructor->id);
                $createdCourses[] = $course;
                $courseIdx++;

                // Dừng khi đã phủ đủ lớn (khoảng 65-75 khóa để tối ưu thời gian)
                if (count($createdCourses) >= 65) {
                    break 2;
                }
            }
        }

        $log('✓ Đã tạo/cập nhật thành công ' . count($createdCourses) . ' Khóa học Demo chuẩn');
        return $createdCourses;
    }

    private function createSingleCourse(array $data, int $categoryId, int $instructorId): Course
    {
        $price = $data['price'] ?? 0;
        $salePrice = $data['sale_price'] ?? null;
        $level = in_array($data['level'] ?? 'beginner', ['beginner', 'intermediate', 'advanced']) ? $data['level'] : 'beginner';

        $course = Course::updateOrCreate(
            ['slug' => $data['slug']],
            [
                'instructor_id' => $instructorId,
                'category_id' => $categoryId,
                'title' => $data['title'],
                'short_description' => Str::limit($data['description'], 150),
                'description' => $data['description'],
                'objectives' => json_encode($data['objectives']),
                'requirements' => json_encode($data['requirements']),
                'target_audience' => json_encode($data['target_audience']),
                'thumbnail' => $data['thumbnail'],
                'preview_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'level' => $level,
                'price' => $price,
                'sale_price' => $salePrice,
                'discount_price' => $salePrice,
                'status' => 'published',
                'is_published' => true,
                'is_featured' => $data['is_featured'] ?? false,
                'rating_avg' => 4.85,
                'rating_count' => rand(15, 95),
                'enrollment_count' => rand(50, 450),
                'duration_minutes' => 360,
                'tags' => json_encode($data['tags']),
                'published_at' => now()->subMonths(3),
                'created_at' => now()->subMonths(4),
                'updated_at' => now(),
            ]
        );

        // Tạo 4 Chương học chuẩn
        $this->seedChaptersAndLessons($course);

        return $course;
    }

    private function seedChaptersAndLessons(Course $course): void
    {
        $chapterTitles = [
            'Chương 1: Tổng quan Nền tảng & Cài đặt Môi trường',
            'Chương 2: Kiến trúc Cốt lõi & Kỹ thuật Xử lý Chuyên sâu',
            'Chương 3: Xây dựng Dự án Thực chiến & Tích hợp Hệ thống',
            'Chương 4: Tối ưu Hiệu năng, Bảo mật & Đánh giá Năng lực',
        ];

        $totalDurationSeconds = 0;

        foreach ($chapterTitles as $cIdx => $cTitle) {
            $chapter = Chapter::updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $cIdx + 1],
                ['title' => $cTitle]
            );

            // Đồng bộ sang course_sections
            $section = CourseSection::updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $cIdx + 1],
                ['title' => $cTitle]
            );

            // Tạo 3 bài học cho mỗi chương: 1 Bài đọc/Tài liệu, 1 Bài Video, 1 Bài Quiz/Thực hành
            // Bài 1: Tài liệu lý thuyết
            $docLesson = Lesson::updateOrCreate(
                ['chapter_id' => $chapter->id, 'sort_order' => 1],
                [
                    'course_id' => $course->id,
                    'section_id' => $section->id,
                    'title' => "Bài " . ($cIdx + 1) . ".1: Tài liệu & Khái niệm trọng tâm - $cTitle",
                    'type' => Lesson::TYPE_DOCUMENT,
                    'content' => "### Hướng dẫn học tập {$course->title}\n\nTrong bài học này, bạn sẽ nắm vững các khái niệm nền tảng, sơ đồ kiến trúc và nguyên tắc thiết kế chuẩn quốc tế.\n\n* **Mục tiêu chính:** Nắm vững lý thuyết cốt lõi\n* **Yêu cầu thực hành:** Ghi chép và chuẩn bị môi trường code",
                    'duration' => 600,
                    'duration_seconds' => 600,
                    'is_preview' => ($cIdx === 0),
                    'is_required' => true,
                    'status' => 'published',
                ]
            );
            $totalDurationSeconds += 600;

            // Bài 2: Video hướng dẫn
            $videoLesson = Lesson::updateOrCreate(
                ['chapter_id' => $chapter->id, 'sort_order' => 2],
                [
                    'course_id' => $course->id,
                    'section_id' => $section->id,
                    'title' => "Bài " . ($cIdx + 1) . ".2: Video Demo Thực hành Trực tiếp - $cTitle",
                    'type' => Lesson::TYPE_VIDEO,
                    'duration' => 900,
                    'duration_seconds' => 900,
                    'is_preview' => ($cIdx === 0),
                    'is_required' => true,
                    'status' => 'published',
                    'upload_status' => 'uploaded',
                    'processing_status' => 'pending',
                ]
            );
            $totalDurationSeconds += 900;

            // Bài 3: Quiz kiểm tra năng lực ở Chương 4 hoặc Bài tập thực hành
            if ($cIdx === 3) {
                $quizLesson = Lesson::updateOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => 3],
                    [
                        'course_id' => $course->id,
                        'section_id' => $section->id,
                        'title' => "Bài 4.3: Bài Kiểm Tra Trắc Nghiệm Tổng Kết Khóa Học",
                        'type' => Lesson::TYPE_QUIZ,
                        'duration' => 1200,
                        'duration_seconds' => 1200,
                        'is_preview' => false,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );
                $totalDurationSeconds += 1200;

                $this->createCourseQuiz($course, $quizLesson);
            } else {
                $practiceLesson = Lesson::updateOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => 3],
                    [
                        'course_id' => $course->id,
                        'section_id' => $section->id,
                        'title' => "Bài " . ($cIdx + 1) . ".3: Bài tập Vận dụng & Thử thách Code",
                        'type' => Lesson::TYPE_DOCUMENT,
                        'content' => "### Bài tập tự rèn luyện\n\nHãy hoàn thành bài tập thực hành theo yêu cầu đã hướng dẫn trong video bài học và đẩy source code lên GitHub cá nhân để lưu trữ portfolio.",
                        'duration' => 600,
                        'duration_seconds' => 600,
                        'is_preview' => false,
                        'is_required' => true,
                        'status' => 'published',
                    ]
                );
                $totalDurationSeconds += 600;
            }
        }

        $course->update(['duration_minutes' => (int) round($totalDurationSeconds / 60)]);
    }

    private function createCourseQuiz(Course $course, Lesson $lesson): void
    {
        $quiz = Quiz::updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'title' => 'Đánh giá Năng lực Tổng kết: ' . $course->title,
                'pass_score' => 75,
                'time_limit_minutes' => 20,
            ]
        );

        $quizVersion = QuizVersion::updateOrCreate(
            ['quiz_id' => $quiz->id, 'version' => 1],
            [
                'title' => $quiz->title,
                'description' => $quiz->description,
                'pass_score' => 75,
                'time_limit_minutes' => 20,
                'max_attempts' => 5,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $quiz->update(['current_published_version_id' => $quizVersion->id]);

        $sampleQuestions = [
            [
                'q' => "Đâu là ưu điểm cốt lõi của công nghệ được giảng dạy trong khóa học: {$course->title}?",
                'options' => [
                    ['text' => 'Khả năng mở rộng cao, cấu trúc module rõ ràng và hiệu năng tối ưu', 'is_correct' => true],
                    ['text' => 'Không cần viết code mà hệ thống tự sinh mã nguồn', 'is_correct' => false],
                    ['text' => 'Chỉ chạy được trên môi trường máy chủ cục bộ', 'is_correct' => false],
                    ['text' => 'Không hỗ trợ kết nối với bất kỳ cơ sở dữ liệu nào', 'is_correct' => false],
                ],
                'exp' => 'Công nghệ hiện đại tập trung vào tính mở rộng, bảo mật và hiệu năng cao trong môi trường Production.'
            ],
            [
                'q' => 'Nguyên tắc thiết kế nào giúp giảm thiểu phụ thuộc và tăng tính tái sử dụng trong dự án?',
                'options' => [
                    ['text' => 'Dependency Injection & Clean Architecture', 'is_correct' => true],
                    ['text' => 'Gộp tất cả logic vào một file duy nhất', 'is_correct' => false],
                    ['text' => 'Bỏ qua việc viết Unit Test và Integration Test', 'is_correct' => false],
                    ['text' => 'Lưu trữ thông tin nhạy cảm trực tiếp vào mã nguồn công khai', 'is_correct' => false],
                ],
                'exp' => 'Dependency Injection và kiến trúc phân lớp Clean Architecture giúp tách biệt mối bận tâm và dễ kiểm thử.'
            ],
            [
                'q' => 'Để bảo vệ ứng dụng trước nguy cơ tấn công mạng, giải pháp nào sau đây là quan trọng nhất?',
                'options' => [
                    ['text' => 'Validate dữ liệu đầu vào nghiêm ngặt, sử dụng Prepared Statements và HTTPS/WAF', 'is_correct' => true],
                    ['text' => 'Tắt toàn bộ cơ chế bảo mật để tăng tốc độ tải trang', 'is_correct' => false],
                    ['text' => 'Cho phép người dùng ẩn danh thao tác vào cơ sở dữ liệu', 'is_correct' => false],
                    ['text' => 'Sử dụng mật khẩu mặc định đơn giản không mã hóa', 'is_correct' => false],
                ],
                'exp' => 'Nguyên tắc phòng thủ theo chiều sâu (Defense-in-depth) yêu cầu validate chặt chẽ mọi input từ người dùng.'
            ],
            [
                'q' => 'Quy trình kiểm thử tự động (Automated Testing) mang lại lợi ích gì cho vòng đời phần mềm?',
                'options' => [
                    ['text' => 'Phát hiện lỗi sớm, đảm bảo tính toàn vẹn khi Refactor và tăng tốc độ Release', 'is_correct' => true],
                    ['text' => 'Làm chậm tiến độ phát triển và không đem lại giá trị gì', 'is_correct' => false],
                    ['text' => 'Chỉ chạy được một lần duy nhất lúc khởi tạo dự án', 'is_correct' => false],
                    ['text' => 'Thay thế hoàn toàn sự sáng tạo của lập trình viên', 'is_correct' => false],
                ],
                'exp' => 'Automated Test giúp phát hiện Regression Bug nhanh chóng trong quy trình CI/CD hiện đại.'
            ],
        ];

        foreach ($sampleQuestions as $qIdx => $item) {
            $question = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz->id, 'sort_order' => $qIdx + 1],
                [
                    'question' => $item['q'],
                    'type' => 'single',
                    'points' => 25,
                    'explanation' => $item['exp'],
                ]
            );

            $questionVersion = QuestionVersion::updateOrCreate(
                ['question_id' => $question->id, 'version' => 1],
                [
                    'question' => $item['q'],
                    'type' => 'single',
                    'points' => 25,
                    'explanation' => $item['exp'],
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            QuizVersionQuestion::updateOrCreate(
                [
                    'quiz_version_id' => $quizVersion->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_version_id' => $questionVersion->id,
                    'sort_order' => $qIdx + 1,
                ]
            );

            foreach ($item['options'] as $oIdx => $opt) {
                QuizOption::updateOrCreate(
                    [
                        'quiz_question_id' => $question->id,
                        'option_text' => $opt['text'],
                    ],
                    [
                        'question_version_id' => $questionVersion->id,
                        'is_correct' => $opt['is_correct'],
                        'explanation' => $opt['is_correct'] ? 'Chính xác!' : 'Sai rồi.',
                    ]
                );
            }
        }
    }
}
